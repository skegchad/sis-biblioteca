<?php
// chat.php - Endpoint del chat con integración a la BD de libros + roles de usuario
// SOLO PARA PRUEBAS
unset($_SESSION["contexto_libros"]);
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
$analisis = analizarConsultaIA(
    $pregunta,
    $_SESSION['historial_chat'] ?? []
);

$intencion = $analisis["intencion"];
$accion = $analisis["accion"];
$seguimiento = $analisis["seguimiento"];
$filtrosBusqueda = $analisis["filtros"];
$indice = $analisis["indice"] ?? null;

// Detectar si el usuario menciona directamente uno de los libros encontrados

if(
    $seguimiento &&
    isset($_SESSION["contexto_libros"]["libros"])
){

    foreach($_SESSION["contexto_libros"]["libros"] as $libro){

        if(
            stripos(
                $pregunta,
                $libro["titulo"]
            ) !== false
        ){

            $_SESSION["contexto_libros"]["ultimo"] = $libro;

            break;
        }
    }
}
$ultimoLibro = $_SESSION["contexto_libros"]["ultimo"] ?? null;

file_put_contents(
    'intencion.txt',
    print_r($analisis,true)
);

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

$ultimoLibro = $_SESSION["contexto_libros"]["ultimo"] ?? null;

switch($accion) {

    
    
    case "mostrar":

        if(isset($_SESSION["contexto_libros"])){

            $respuestaDirectaLibros =
            formatearRespuestaLibros(
                $_SESSION["contexto_libros"]["libros"]
            );

        }else{

            $contexto="No hay libros anteriores seleccionados.";

        }

    break;
    
    case "descripcion":

        if($ultimoLibro){

            $respuestaDirectaLibros =
            "📖 \"{$ultimoLibro['titulo']}\"\n\n".
            $ultimoLibro['descripcion'];

        }

    break;

    case "tipo":

        if(isset($_SESSION["contexto_libros"]["ultimo"])){

            $libro = $_SESSION["contexto_libros"]["ultimo"];

            $respuestaDirectaLibros =
            "\"{$libro['titulo']}\" es de tipo {$libro['tipo']}.";

        }

    break;

    case "idioma":

        if(isset($_SESSION["contexto_libros"]["ultimo"])){

            $libro = $_SESSION["contexto_libros"]["ultimo"];

            $respuestaDirectaLibros =
            "El libro \"{$libro['titulo']}\" está escrito en {$libro['idioma']}.";

        }

    break;

    case "ano":

        if(isset($_SESSION["contexto_libros"]["ultimo"])){

            $libro = $_SESSION["contexto_libros"]["ultimo"];

            $respuestaDirectaLibros =
            "El libro \"{$libro['titulo']}\" fue publicado en {$libro['ano']}.";

        }

    break;

    case "editorial":

        if(isset($_SESSION["contexto_libros"]["ultimo"])){

            $libro = $_SESSION["contexto_libros"]["ultimo"];

            $respuestaDirectaLibros =
            "Fue publicado por {$libro['editorial']}.";

        }

    break;

    case "categoria":

        if(isset($_SESSION["contexto_libros"]["ultimo"])){

            $libro = $_SESSION["contexto_libros"]["ultimo"];

            $respuestaDirectaLibros =
            "\"{$libro['titulo']}\" pertenece a la categoría {$libro['categoria']}.";

        }

    break;
    
    case "edicion":
        if (isset($_SESSION["contexto_libros"]["ultimo"])) {
            $libro = $_SESSION["contexto_libros"]["ultimo"];
            $respuestaDirectaLibros = !empty($libro['edicion'])
                ? "La edición de \"{$libro['titulo']}\" es: {$libro['edicion']}."
                : "No hay una edición registrada para este libro.";
        }
    break;

    case "temas":

        if(isset($_SESSION["contexto_libros"]["ultimo"])){

            $libro = $_SESSION["contexto_libros"]["ultimo"];

            if(!empty($libro['temas'])){

                $respuestaDirectaLibros =
                "\"{$libro['titulo']}\" trata sobre: {$libro['temas']}.";

            }else{

                $respuestaDirectaLibros =
                "No hay temas registrados para este libro.";

            }

        }

    break;

    case "subcategoria":

        if(isset($_SESSION["contexto_libros"]["ultimo"])){

            $libro = $_SESSION["contexto_libros"]["ultimo"];

            if(!empty($libro['subcategoria'])){

                $respuestaDirectaLibros =
                "\"{$libro['titulo']}\" pertenece a la subcategoría {$libro['subcategoria']}.";

            }else{

                $respuestaDirectaLibros =
                "No hay una subcategoría registrada para este libro.";

            }

        }

    break;

    case "autor":

        if(isset($_SESSION["contexto_libros"]["ultimo"])){

            $libro =
            $_SESSION["contexto_libros"]["ultimo"];


            $respuestaDirectaLibros =
            "El autor de \"{$libro['titulo']}\" es {$libro['autor']}.";

        }

    break;

    case "disponibilidad":


        if(isset($_SESSION["contexto_libros"]["ultimo"])){

            $libro =
            $_SESSION["contexto_libros"]["ultimo"];


            $disponibles =
            $libro['ejemplares'] -
            $libro['prestados'];


            if($disponibles > 0){

                $respuestaDirectaLibros =
                "Sí, está disponible. Hay {$disponibles} ejemplares libres.";

            }else{

                $respuestaDirectaLibros =
                "Actualmente todos los ejemplares están prestados.";

            }

        }


    break;
    
    case "ubicacion":


        if(isset($_SESSION["contexto_libros"]["ultimo"])){

            $libro =
            $_SESSION["contexto_libros"]["ultimo"];


            $respuestaDirectaLibros =
            "Puedes encontrarlo en la sección {$libro['seccion']}, bloque {$libro['bloque']} y código CDD {$libro['cdd']}.";

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
            $palabras = array_values(array_filter(
                preg_split('/[^\p{L}0-9]+/u', $pregunta),
                fn($p) => mb_strlen($p) >= 3
            ));
            $usuarios = buscarUsuariosRelevantes($pdo, $palabras);
            $contexto = !empty($usuarios)
                ? construirContextoUsuarios($usuarios)
                : "No se encontraron usuarios.";
        } else {
            $contexto = "El usuario no tiene permisos para consultar usuarios.";
        }
    break;

    case "ninguna":
        if ($intencion === "saludo") {
            $contexto = "El usuario está saludando. Responde de forma amigable.";
        } elseif ($intencion === "social") {
            $contexto = "El usuario está conversando de forma casual. Responde naturalmente.";
        }
    break;

    case "similares":

        if(isset($_SESSION["contexto_libros"]["ultimo"])){

            $libro =
            $_SESSION["contexto_libros"]["ultimo"];


            $recomendados =
            buscarLibrosSimilares($pdo,$libro);


            if(!empty($recomendados)){


                $_SESSION["contexto_libros"]=[

                    "libros"=>$recomendados,

                    "ultimo"=>$recomendados[0]

                ];


                $respuestaDirectaLibros =
                formatearRespuestaLibros($recomendados);


            }else{

                $sinRecomendaciones=true;

            }

        }

    break;

    case "buscar":

        $libros = buscarLibrosRelevantes(
            $pdo,
            $filtrosBusqueda
        );

        if(!empty($libros)){

            $_SESSION["contexto_libros"]=[

                "consulta"=>$pregunta,
                "libros"=>$libros,
                "ultimo"=>$libros[0]

            ];

            $respuestaDirectaLibros =
                formatearRespuestaLibros($libros);

        }else{

            $sinResultadosLibros=true;

        }

    break;

    default:

        if(!empty($filtrosBusqueda)){

            $libros = buscarLibrosRelevantes(
                $pdo,
                $filtrosBusqueda
            );

            if (!empty($libros)) {

                $_SESSION["contexto_libros"] = [

                    "consulta"=>$pregunta,
                    "libros"=>$libros,
                    "ultimo"=>$libros[0]

                ];

                $respuestaDirectaLibros =
                formatearRespuestaLibros($libros);

            }

        }

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
$_SESSION['historial_chat'] = array_slice($_SESSION['historial_chat'], -6);

echo json_encode(['respuesta' => $respuesta]);

$temas = $pdo->query("
    SELECT nombre
    FROM temas
    ORDER BY nombre
")->fetchAll(PDO::FETCH_COLUMN);

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


INFORMACIÓN SOBRE BMO:

Eres BMO, el asistente virtual de una biblioteca.

Puedes ayudar con:
- Buscar libros registrados en la biblioteca.
- Dar información de libros encontrados.
- Explicar datos de libros.
- Orientar al usuario sobre las funciones disponibles.

No inventes datos específicos de libros.
Si el usuario pregunta por cantidades, estadísticas o información exacta de la biblioteca, solo usa los datos que aparezcan en el contexto.


Pregunta del usuario:

$pregunta


Respuesta:
PROMPT;
}

function buscarLibroBaseSimilitud(PDO $pdo, array $filtros): array
{
    if(empty($filtros)){
        return [];
    }

    return buscarLibrosRelevantes($pdo, $filtros);
}

function buscarLibrosSimilares(PDO $pdo,array $libro): array
{

    $temas = array_filter(array_map('trim', explode(',', $libro['temas'] ?? '')));
    if (empty($temas)) {
        return [];
    }


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
function analizarConsultaIA(string $pregunta, array $historial = []): array
{
    $prompt = <<<PROMPT
Eres el cerebro de un asistente de biblioteca.

Tu única función es analizar la intención del usuario y devolver una estructura JSON.

NO respondas al usuario.
NO expliques nada.
NO agregues texto.
Devuelve EXCLUSIVAMENTE JSON válido.

Formato obligatorio:

{
  "intencion":"",
  "seguimiento":false,
  "accion":"",
  "indice":null,
  "filtros":[]
}


=========================
INTENCIONES DISPONIBLES
=========================

Las intenciones posibles son:

- libros
  Consultas relacionadas con libros, autores, títulos, categorías, características o disponibilidad.

- similares
  Solicitud de libros parecidos o recomendaciones.

- usuarios
  Consultas sobre usuarios (solo administradores).

- estadisticas
  Consultas estadísticas de la biblioteca.

- saludo
  Saludos como hola, buenos días, etc.

- social
  Conversación casual.

- general
  Preguntas que no pertenecen a ninguna categoría anterior.


=========================
ACCIONES DISPONIBLES
=========================

buscar
Buscar libros nuevos en la base de datos.

mostrar
Mostrar los libros encontrados anteriormente.

descripcion
Obtener la descripción del libro actual.

autor
Obtener el autor del libro actual.

disponibilidad
Consultar si el libro está disponible.

ubicacion
Consultar dónde está ubicado el libro.

idioma
Consultar el idioma del libro.

ano
Consultar el año de publicación.

editorial
Consultar la editorial.

categoria
Consultar la categoría Dewey o clasificación general.

subcategoria
Consultar la subcategoría.

tipo
Consultar el tipo de libro.
(Ejemplo: Novela, Obra Filosófica, Manual, Enciclopedia)

temas
Consultar los temas relacionados del libro.

edicion
Consultar la edición.

similares
Buscar libros similares.

estadisticas
Consultar estadísticas.

usuarios
Consultar usuarios.

ninguna
Cuando no corresponda ninguna acción.


=========================
FILTROS DE BÚSQUEDA
=========================

Los filtros sirven SOLO cuando la acción sea "buscar".

Formato:

{
 "campo":"autor",
 "valor":"Kafka"
}


Campos permitidos:

titulo
autor
temas
tipo
categoria
subcategoria
idioma
editorial
edicion
ano
cdd
bloque
seccion

Ejemplo de estadísticas:
Usuario:
¿Cuántos libros tienes en la biblioteca?

Respuesta:

{
 "intencion":"estadisticas",
 "seguimiento":false,
 "accion":"estadisticas",
 "filtros":[]
}

REGLAS IMPORTANTES:

PRIORIDAD DE INTENCIONES:

1. Primero determina si el usuario está buscando libros nuevos.
2. Después revisa si está hablando del libro anterior.

Si la pregunta contiene palabras como:
- qué libros
- que libros
- tienes libros
- libros de
- libros sobre
- libros que traten
- recomiéndame libros
- busca libros
- dame libros

SIEMPRE es una nueva búsqueda.

En esos casos:
seguimiento=false
accion="buscar"

Aunque exista un libro anterior en el historial.


Ejemplos:

Usuario:
¿Qué libros que traten de soledad tienes?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":false,
 "accion":"buscar",
 "filtros":[
   {
     "campo":"temas",
     "valor":"soledad"
   }
 ]
}


Usuario:
¿Qué libros sobre filosofía tienes?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":false,
 "accion":"buscar",
 "filtros":[
   {
     "campo":"temas",
     "valor":"filosofia"
   }
 ]
}


Usuario:
¿Qué libros de Kafka tienes?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":false,
 "accion":"buscar",
 "filtros":[
   {
     "campo":"autor",
     "valor":"Kafka"
   }
 ]
}


Solo usa seguimiento=true cuando el usuario NO está buscando libros nuevos.

Ejemplos:

Usuario:
¿Cuál es su editorial?

Respuesta:
{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"editorial",
 "filtros":[]
}


Usuario:
Su descripción

Respuesta:
{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"descripcion",
 "filtros":[]
}


Ejemplos:


Usuario:
¿Qué libros tienes de Kafka?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":false,
 "accion":"buscar",
 "filtros":[
   {
    "campo":"autor",
    "valor":"Kafka"
   }
 ]
}


Usuario:
¿Qué libros hay de Nietzsche?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":false,
 "accion":"buscar",
 "filtros":[
   {
    "campo":"autor",
    "valor":"Nietzsche"
   }
 ]
}


Usuario:
¿Qué libros hablan de soledad?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":false,
 "accion":"buscar",
 "filtros":[
   {
    "campo":"temas",
    "valor":"soledad"
   }
 ]
}


Usuario:
¿Qué novelas tienes?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":false,
 "accion":"buscar",
 "filtros":[
   {
    "campo":"tipo",
    "valor":"Novela"
   }
 ]
}


Usuario:
¿Qué libros de filosofía tienes?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":false,
 "accion":"buscar",
 "filtros":[
   {
    "campo":"categoria",
    "valor":"Filosofía"
   }
 ]
}


=========================
CONSULTAS SOBRE EL LIBRO ACTUAL
=========================

Cuando el usuario habla de "el libro", "su", "ese", "él", "este libro", significa que continúa hablando del último libro mostrado.

En estos casos:

seguimiento=true


Ejemplos:



Usuario:
Su descripción

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"descripcion",
 "filtros":[]
}


Usuario:
¿Quién lo escribió?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"autor",
 "filtros":[]
}


Usuario:
¿En qué año fue publicado?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"ano",
 "filtros":[]
}


Usuario:
¿Qué idioma tiene?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"idioma",
 "filtros":[]
}


Usuario:
¿Qué editorial es?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"editorial",
 "filtros":[]
}


Usuario:
¿Cuál es su categoría?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"categoria",
 "filtros":[]
}


Usuario:
¿Y su subcategoría?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"subcategoria",
 "filtros":[]
}


Usuario:
¿Qué tipo de libro es?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"tipo",
 "filtros":[]
}


Usuario:
¿Está disponible?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"disponibilidad",
 "filtros":[]
}


Usuario:
¿Dónde está ubicado?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"ubicacion",
 "filtros":[]
}


Usuario:
¿Cuáles son sus temas?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"temas",
 "filtros":[]
}


Usuario:
¿Cuál es la edición?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"edicion",
 "filtros":[]
}


=========================
SEGUIMIENTO
=========================


Usuario:
¿Cuál?

Respuesta:

{
 "intencion":"libros",
 "seguimiento":true,
 "accion":"mostrar",
 "filtros":[]
}


Usuario:
Recomiéndame libros parecidos

Respuesta:

{
 "intencion":"similares",
 "seguimiento":true,
 "accion":"similares",
 "filtros":[]
}


=========================
SALUDOS
=========================


Usuario:
Hola

Respuesta:

{
 "intencion":"saludo",
 "seguimiento":false,
 "accion":"ninguna",
 "filtros":[]
}


=========================

Ahora analiza esta consulta:

{$pregunta}

Historial reciente:

PROMPT;
foreach($historial as $mensaje){

    $prompt .=
    "\n".$mensaje["rol"].": ".$mensaje["texto"];

}

$prompt .= <<<PROMPT


Responde solamente JSON.

Respuesta:
PROMPT;

    $respuesta = preguntarOllama($prompt);

    $respuesta = str_replace(
        ['```json','```'],
        '',
        $respuesta
    );

    $json = null;
    if (preg_match('/\{.*\}/s', $respuesta, $match)) {
        $json = json_decode($match[0], true);
    }

    $default = [
        "intencion" => "general", "seguimiento" => false,
        "accion" => "ninguna", "indice" => null, "filtros" => []
    ];

    return is_array($json) ? ($json + $default) : $default;
}