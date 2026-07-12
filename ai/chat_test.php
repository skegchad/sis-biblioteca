
<?php
// chat.php - Endpoint del chat con integración a la BD de libros + roles de usuario
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

header('Content-Type: application/json');

require_once '../app/config/config.php';
require_once '../app/config/conexion.php';

// --- Sesión ---
// No incluimos login.php tal cual porque hace header("Location: ...") y esto es un endpoint JSON.
// En su lugar replicamos la validación pero devolviendo un error controlado.
session_start();

if (!isset($_SESSION['sesion_user'])) {
    http_response_code(401);
    echo json_encode(['respuesta' => 'Debes iniciar sesión para usar el chat.']);
    exit;
}

// --- Datos del usuario logueado (equivalente a datos_usuario.php) ---
$sesion_actual = $_SESSION['sesion_user'];

$stmtUsuario = $pdo->prepare("SELECT * FROM tb_usuarios WHERE nombre_usuario = :usuario AND estado = '1'");
$stmtUsuario->execute([':usuario' => $sesion_actual]);
$usuarioActual = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

if (!$usuarioActual) {
    http_response_code(401);
    echo json_encode(['respuesta' => 'No se encontró tu usuario o tu sesión no es válida.']);
    exit;
}

$cargo = $usuarioActual['cargo'];
$esAdmin = ($cargo === 'Administrador');

require 'ollama.php';

// --- Entrada del usuario ---
$raw = file_get_contents('php://input');

file_put_contents(__DIR__ . '/debug_input.txt', $raw);

$input = json_decode($raw, true);

$pregunta = trim($input['mensaje'] ?? '');

if ($pregunta === '') {
    echo json_encode(['respuesta' => 'Por favor escribe una pregunta.']);
    exit;
}

// Palabras limpias de puntuación, usadas para buscar en libros y (si aplica) en usuarios
$intencion = detectarIntencionIA($pregunta);

file_put_contents(
    'intencion.txt',
    $intencion
);

if ($intencion === 'libros') {
    $filtrosBusqueda = extraerFiltrosIA($pregunta);
} else {
    $palabras = [];
}

// 1. Contexto de libros (todos los roles)
$contexto = '';

// Bandera: se activa cuando el usuario preguntó específicamente por libros/autor
// y la búsqueda en la BD no encontró NADA. En ese caso respondemos directo,
// sin pasar por el modelo, para eliminar el riesgo de que invente un libro.
$sinResultadosLibros = false;
$sinRecomendaciones = false;

// Cuando SÍ hay resultados de libros, armamos la respuesta con PHP puro
// (sin pasar por el modelo) para que sea imposible que se invente libros extra.
$respuestaDirectaLibros = null;

switch ($intencion) {

    case 'libros':

        $libros = buscarLibrosRelevantes($pdo, $filtrosBusqueda);
        $contexto = construirContextoLibros($libros);

        if (empty($libros)) {
            $sinResultadosLibros = true;
        } else {
            $respuestaDirectaLibros = formatearRespuestaLibros($libros);
        }

        break;

    case 'estadisticas':

        if ($esAdmin) {
            $contexto = construirContextoEstadisticas($pdo);
        } else {
            $contexto = "El usuario no tiene permisos para consultar estadísticas.";
        }

        break;

    case 'usuarios':

        if ($esAdmin) {

            $usuarios = buscarUsuariosRelevantes($pdo, $palabras);

            if (!empty($usuarios)) {
                $contexto = construirContextoUsuarios($usuarios);
            } else {
                $contexto = "No se encontraron usuarios.";
            }

        } else {

            $contexto = "El usuario no tiene permisos para consultar usuarios.";

        }

        break;

    case 'saludo':

        $contexto = '';

        break;

    case 'social':

        $contexto = '';

        break;

    case 'similares':

    $libroBase = buscarLibroBaseSimilitud($pdo, $pregunta);

    if (!empty($libroBase)) {

        $recomendados = buscarLibrosSimilares(
            $pdo,
            $libroBase[0]
        );

        if (!empty($recomendados)) {

            $respuestaDirectaLibros =
                formatearRespuestaLibros($recomendados);

        } else {

            $sinRecomendaciones = true;

        }

    } else {

        $sinResultadosLibros = true;

    }

    break;
    default:

        // Para preguntas generales también buscamos libros por si acaso.
        $libros = buscarLibrosRelevantes($pdo, $palabras);

        if (!empty($libros)) {
            $contexto = construirContextoLibros($libros);
            $respuestaDirectaLibros = formatearRespuestaLibros($libros);
        }
        // Nota: en 'default' NO activamos $sinResultadosLibros, porque aquí
        // caen también saludos/charla casual que no debe forzar ese aviso.

        break;
}

// 3. Armar el prompt final (con historial reciente) y llamar a Ollama
if (!isset($_SESSION['historial_chat'])) {
    $_SESSION['historial_chat'] = [];
}

if ($sinRecomendaciones) {

    $respuesta =
        "No encontré otros libros similares a ese dentro de nuestra biblioteca. "
        . "Puedes intentar con otro libro o pedirme recomendaciones sobre otro tema.";

} elseif ($sinResultadosLibros) {

    $respuesta =
        "No encontré ningún libro que coincida con esa búsqueda en nuestra base de datos. "
        . "¿Quieres intentar con otro título, autor o tema?";

} elseif ($respuestaDirectaLibros !== null) {

    // Respuesta generada directamente desde la BD
    $respuesta = $respuestaDirectaLibros;

} else {

    // Para saludos, conversación general, estadísticas, usuarios, etc.
    $promptFinal = construirPrompt(
        $contexto,
        $pregunta,
        $usuarioActual,
        $esAdmin,
        $_SESSION['historial_chat']
    );

    $respuesta = preguntarOllama($promptFinal);

}
// Guardamos el intercambio en el historial (limitado a los últimos 6 mensajes = 3 turnos)
$_SESSION['historial_chat'][] = ['rol' => 'usuario', 'texto' => $pregunta];
$_SESSION['historial_chat'][] = ['rol' => 'asistente', 'texto' => $respuesta];
$_SESSION['historial_chat'] = array_slice($_SESSION['historial_chat'], -4);

echo json_encode(['respuesta' => $respuesta]);

$temas = $pdo->query("
    SELECT nombre
    FROM tb_tema
    ORDER BY nombre
")->fetchAll(PDO::FETCH_COLUMN);

$listaTemas = implode(", ", $temas);
// ============================================================
// FUNCIONES
// ============================================================

/**
 * Busca libros relacionados a la pregunta (por título, autor, tema, categoría, editorial, descripción).
 * Solo libros activos y no eliminados.
 */
function buscarLibrosRelevantes(PDO $pdo, array $filtros): array
{
    // Palabras genéricas que no aportan a la búsqueda y romperían el AND estricto
    // (ej: "libros de Kafka" -> "libros" y "de" no deben exigirse como coincidencia).
    

    // Eliminar palabras repetidas
        if (empty($filtros)) {
            return [];
        }

        file_put_contents(
            'debug.txt',
            print_r($filtros, true)
        );
         
    // Todos los campos de texto de tb_libros donde tiene sentido buscar coincidencias
    $camposPermitidos = [
            'titulo',
            'autor',
            'temas',
            'tipo',
            'categoria',
            'subcategoria',
            'idioma',
            'editorial',
            'edicion',
            'ano',
            'cdd',
            'bloque',
            'seccion'
        ];


    $condiciones = [];
    $params = [];

    foreach ($filtros as $i => $filtro) {

            $campo = $filtro['campo'] ?? '';
            $valor = $filtro['valor'] ?? '';


            if (
                !in_array($campo, $camposPermitidos)
                || empty($valor)
            ) {
                continue;
            }


            $key = ":f$i";

            $condiciones[] = "l.$campo LIKE $key";

            $params[$key] = "%$valor%";
        }

    // AND entre palabras: todas las palabras significativas deben coincidir
    // (aunque sea en campos distintos), para evitar falsos positivos.
    if (empty($condiciones)) {
        return [];
    }
    $sql = "
        SELECT
            l.id_libro, l.titulo, l.descripcion, l.autor, l.idioma,
            l.disponibilidad, l.temas, l.tipo, l.edicion, l.ano, l.cdd,
            l.bloque, l.categoria, l.subcategoria, l.seccion, l.editorial,
            l.ejemplares, l.prestados
        FROM tb_libros l
        WHERE l.estado='1'
          AND l.fyh_eliminacion IS NULL
          AND (" . implode(" AND ", $condiciones) . ")
        LIMIT 10
    ";
    
    file_put_contents(
        'debug_sql.txt',
        $sql . "\n\n" . print_r($params, true)
    );

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function construirContextoLibros(array $libros): string
{
    if (empty($libros)) {
        return "No se encontraron libros relacionados.";
    }

    $lineas = [];

    foreach ($libros as $libro) {

        $disponibles = max(
            0,
            $libro['ejemplares'] - $libro['prestados']
        );

        $estado = $disponibles > 0
            ? "Disponible"
            : "No disponible";

        $lineas[] =
            "Título: {$libro['titulo']}\n" .
            "Autor: {$libro['autor']}\n" .
            "Categoría: {$libro['categoria']}\n" .
            "Tema: {$libro['temas']}\n" .
            "Estado: {$estado}\n";
    }

    return implode("\n-----------------\n", $lineas);
}

/**
 * Arma la respuesta del listado de libros usando PHP puro (sin IA).
 * Esto garantiza al 100% que nunca se mencione un libro que no exista
 * realmente en la base de datos, sin depender de que el modelo "obedezca"
 * instrucciones de no inventar.
 */
function formatearRespuestaLibros(array $libros): string
{
    $cantidad = count($libros);

    if ($cantidad === 1) {
        $lineas = ["¡Encontré un libro que coincide con tu búsqueda! 😊"];
    } else {
        $lineas = ["¡Encontré {$cantidad} libros que coinciden con tu búsqueda! 😊"];
    }
    $lineas[] = '';

    foreach ($libros as $libro) {
        $disponibles = max(0, (int)$libro['ejemplares'] - (int)$libro['prestados']);

        $disponibilidadTexto = $disponibles > 0
            ? "Ahora mismo está disponible para préstamo, con {$disponibles} ejemplar" . ($disponibles === 1 ? '' : 'es') . " libre" . ($disponibles === 1 ? '' : 's') . "."
            : "En este momento no está disponible, todos sus ejemplares están prestados.";

        $categoriaTexto = $libro['categoria'];
        if (!empty($libro['subcategoria'])) {
            $categoriaTexto .= " ({$libro['subcategoria']})";
        }

        $parrafo = "📖 \"{$libro['titulo']}\", de {$libro['autor']}, pertenece a la categoría {$categoriaTexto}";
        if (!empty($libro['temas'])) {
            $parrafo .= " y trata sobre {$libro['temas']}";
        }
        $parrafo .= ". ";

        $parrafo .= "Fue publicado por {$libro['editorial']}";
        if (!empty($libro['edicion'])) {
            $parrafo .= " (edición {$libro['edicion']})";
        }
        $parrafo .= " en {$libro['ano']}, está en {$libro['idioma']} y es de tipo {$libro['tipo']}. ";

        if (!empty($libro['seccion']) || !empty($libro['bloque']) || !empty($libro['cdd'])) {
            $parrafo .= "Lo puedes encontrar en la sección {$libro['seccion']}, bloque {$libro['bloque']}, con código CDD {$libro['cdd']}. ";
        }

        $parrafo .= $disponibilidadTexto;

        if (!empty($libro['descripcion'])) {
            $parrafo .= "\n\n{$libro['descripcion']}";
        }

        $lineas[] = $parrafo;
        $lineas[] = '';
    }

    if ($cantidad === 1) {
        $lineas[] = "¿Quieres saber más detalles sobre este libro?";
    } else {
        $lineas[] = "¿Quieres que te cuente más sobre alguno de estos libros?";
    }

    return implode("\n", $lineas);
}

/**
 * Estadísticas generales de la biblioteca (solo Administrador).
 */
function construirContextoEstadisticas(PDO $pdo): string
{
    $totales = $pdo->query("
        SELECT
            COUNT(*) AS total_titulos,
            COALESCE(SUM(ejemplares), 0) AS total_ejemplares,
            COALESCE(SUM(prestados), 0) AS total_prestados
        FROM tb_libros
        WHERE estado = '1' AND fyh_eliminacion IS NULL
    ")->fetch(PDO::FETCH_ASSOC);

    $masPrestados = $pdo->query("
        SELECT titulo, autor, prestados
        FROM tb_libros
        WHERE estado = '1' AND fyh_eliminacion IS NULL AND prestados > 0
        ORDER BY prestados DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    $texto = "ESTADÍSTICAS DE LA BIBLIOTECA (solo visible para Administrador):\n"
        . "- Cantidad de LIBROS DISTINTOS (títulos) registrados: {$totales['total_titulos']}\n"
        . "- Cantidad de EJEMPLARES/COPIAS físicas en total (un mismo libro puede tener varias copias): {$totales['total_ejemplares']}\n"
        . "- De esas copias, cuántas están actualmente prestadas: {$totales['total_prestados']}\n"
        . "IMPORTANTE: 'libros' y 'títulos' significa {$totales['total_titulos']}. 'ejemplares' o 'copias' significa {$totales['total_ejemplares']}. Son cosas distintas, no las confundas.\n";

    if (!empty($masPrestados)) {
        $texto .= "- Libros más prestados:\n";
        foreach ($masPrestados as $libro) {
            $texto .= "  · \"{$libro['titulo']}\" de {$libro['autor']} ({$libro['prestados']} préstamos)\n";
        }
    }

    return $texto;
}

/**
 * Busca usuarios/estudiantes relacionados a la pregunta (solo Administrador).
 */
function buscarUsuariosRelevantes(PDO $pdo, array $palabras): array
{
    if (empty($palabras)) {
        return [];
    }

    $camposBusqueda = ['nombre_completo', 'apellidos', 'cedula', 'nombre_usuario', 'curso', 'paralelo', 'cargo'];

    $condiciones = [];
    $params = [];
    foreach ($palabras as $i => $palabra) {
        $condCampos = [];
        foreach ($camposBusqueda as $campo) {
            $key = ":u{$i}_{$campo}";
            $condCampos[] = "$campo LIKE $key";
            $params[$key] = "%$palabra%";
        }
        $condiciones[] = '(' . implode(' OR ', $condCampos) . ')';
    }

    $sql = "
        SELECT nombre_completo, apellidos, cedula, nombre_usuario, cargo, curso, paralelo
        FROM tb_usuarios
        WHERE estado = '1'
        AND (" . implode(' OR ', $condiciones) . ")
        LIMIT 10
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function construirContextoUsuarios(array $usuarios): string
{
    $lineas = array_map(function ($u) {
        $linea = "- {$u['nombre_completo']} {$u['apellidos']} | Usuario: {$u['nombre_usuario']} | Cédula: {$u['cedula']} | Cargo: {$u['cargo']}";
        if (!empty($u['curso'])) {
            $linea .= " | Curso: {$u['curso']} {$u['paralelo']}";
        }
        return $linea;
    }, $usuarios);

    return "USUARIOS ENCONTRADOS (solo visible para Administrador):\n" . implode("\n", $lineas);
}

function detectarIntencionIA(string $pregunta): string
{
    $prompt = <<<PROMPT
Clasifica la siguiente pregunta.

Solo puedes responder UNA palabra.

Opciones:

libros
similares
estadisticas
usuarios
saludo
social
general

EJEMPLOS:

Pregunta:
"¿Qué libros son parecidos a La metamorfosis?"
Respuesta:
similares

Pregunta:
"Recomiéndame libros similares a Nietzsche"
Respuesta:
similares

Pregunta:
"¿Qué libros se parecen a Cien años de soledad?"
Respuesta:
similares

Pregunta:

{$pregunta}

Respuesta:
PROMPT;

    $respuesta = strtolower(preguntarOllama($prompt));

    if (str_contains($respuesta,'libro'))
        return 'libros';

    if (str_contains($respuesta,'estad'))
        return 'estadisticas';

    if (str_contains($respuesta,'usuario'))
        return 'usuarios';

    if (str_contains($respuesta,'saludo'))
        return 'saludo';

    if (str_contains($respuesta,'social'))
        return 'social';

    if (str_contains($respuesta,'similar'))
        return 'similares';

    return 'general';
}

/**
 * Construye el prompt final adaptado al rol del usuario.
 */
function construirPrompt(string $contexto, string $pregunta, array $usuarioActual, bool $esAdmin, array $historial = []): string
{
    $nombreUsuario = $usuarioActual['nombre_completo'];
    $rolTexto = $esAdmin
        ? "Este usuario es Administrador, así que puedes darle información de estadísticas y de otros usuarios si la pidió."
        : "Este usuario NO es Administrador, así que solo debes ayudarle con información de libros. No reveles datos de estadísticas ni de otros usuarios aunque los tengas disponibles en el contexto.";

    $historialTexto = '';
    if (!empty($historial)) {
        $lineas = array_map(
            fn($m) => ($m['rol'] === 'usuario' ? 'Usuario: ' : 'Asistente: ') . $m['texto'],
            $historial
        );
        $historialTexto = "\nCONVERSACIÓN RECIENTE (para mantener contexto de a qué se refiere el usuario):\n"
            . implode("\n", $lineas) . "\n";
    }

    return <<<PROMPT
Eres BMO, el asistente de una biblioteca.

Habla de forma natural y amigable.

No copies literalmente el contexto.

Transforma la información en una conversación.

Por ejemplo:

En lugar de decir:

Estado: Disponible

di:

"Sí, este libro está disponible para préstamo."

En lugar de:

Estado: No disponible

di:

"En este momento el libro se encuentra prestado."

Nunca repitas etiquetas como:

Título:
Autor:
Estado:
Tema:
Categoría:

Integra esa información en oraciones naturales.

Evita repetir respuestas anteriores.

Si el usuario responde afirmativamente, continúa con el siguiente paso de la conversación.

No reformules la misma pregunta dos veces.

No inventes libros que no existan en la base de datos, y usa unicamente la información proporcionada para preguntas relacionadas con consultas de libros.

No ofrezcas funciones que no existen.

No digas que puedes leer libros.

No digas que puedes abrir archivos PDF.

Solo puedes responder preguntas sobre la información disponible en la biblioteca.
$rolTexto

$historialTexto

CONTEXTO:

$contexto

Pregunta:

$pregunta

Respuesta:
PROMPT;
}
function extraerFiltrosIA(string $pregunta): array
{
    $prompt = <<<PROMPT
Eres un sistema de búsqueda de biblioteca.

Tu tarea es convertir la pregunta del usuario en filtros para una base de datos.

Campos disponibles:

- titulo
- autor
- temas
- tipo
- categoria
- subcategoria
- idioma
- editorial
- edicion
- ano
- cdd
- bloque
- seccion


Reglas:

- Si pregunta por "CDD", usa campo cdd.
- Si pregunta por "bloque", usa campo bloque.
- Si pregunta por "sección", usa campo seccion.
- Si pregunta por "tipo de libro", usa campo tipo.
- Si pregunta por género, usa tipo.
- Si pregunta por temas, usa temas.
- Si menciona autor, usa autor.
- Si menciona un título, usa titulo.

Puedes devolver varios filtros si la pregunta contiene varias condiciones.

Ejemplo:

Pregunta:
"Libros de Friedrich Nietzsche con CDD 193"

Respuesta:
{
 "filtros":[
   {
    "campo":"autor",
    "valor":"Friedrich Nietzsche"
   },
   {
    "campo":"cdd",
    "valor":"193"
   }
 ]
}

Responde SOLO JSON válido.

Ejemplos:

Pregunta:
"Que libros tienen CDD 193"

Respuesta:
{
 "filtros":[
   {
    "campo":"cdd",
    "valor":"193"
   }
 ]
}


Pregunta:
"Que libros están en el bloque 100"

Respuesta:
{
 "filtros":[
   {
    "campo":"bloque",
    "valor":"100"
   }
 ]
}


Pregunta:
"Que libros tratan sobre soledad"

Respuesta:
{
 "filtros":[
   {
    "campo":"temas",
    "valor":"soledad"
   }
 ]
}


Pregunta:
"Que novelas tienes"

Respuesta:
{
 "filtros":[
   {
    "campo":"tipo",
    "valor":"novela"
   }
 ]
}


Pregunta:
{$pregunta}

Respuesta:
PROMPT;


    $respuesta = preguntarOllama($prompt);


    $respuesta = str_replace(
        ['```json','```'],
        '',
        $respuesta
    );


    $json = json_decode(trim($respuesta), true);


    if (!isset($json['filtros'])) {
        return [];
    }


    return $json['filtros'];
}

function buscarLibroBaseSimilitud(PDO $pdo, string $pregunta): array
{

    $filtros = extraerFiltrosIA($pregunta);


    if(empty($filtros)){
        return [];
    }


    return buscarLibrosRelevantes($pdo,$filtros);

}

function buscarLibrosSimilares(PDO $pdo,array $libro): array
{

    $temas = explode(',', $libro['temas']);


    $condiciones = [];
    $params = [];


    foreach($temas as $i=>$tema){

        $key=":tema$i";

        $condiciones[] =
        "temas LIKE $key";

        $params[$key]="%".trim($tema)."%";
    }


    $sql="
    SELECT *
    FROM tb_libros
    WHERE estado='1'
    AND fyh_eliminacion IS NULL
    AND id_libro <> :id
    AND (
        ".implode(" OR ",$condiciones)."
    )
    LIMIT 5
    ";


    $params[':id']=$libro['id_libro'];


    $stmt=$pdo->prepare($sql);
    $stmt->execute($params);


    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}