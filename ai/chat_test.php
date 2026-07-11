<?php
// chat.php - Endpoint del chat con integración a la BD de libros + roles de usuario

header('Content-Type: application/json');

require '../app/config/config.php';    // define las constantes BD_SISTEMA, BD_SERVIDOR, etc.
require '../app/config/conexion.php';  // usa esas constantes y crea $pdo

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
$input = json_decode(file_get_contents('php://input'), true);
$pregunta = trim($input['mensaje'] ?? '');

if ($pregunta === '') {
    echo json_encode(['respuesta' => 'Por favor escribe una pregunta.']);
    exit;
}

// Palabras limpias de puntuación, usadas para buscar en libros y (si aplica) en usuarios
$textoLimpio = preg_replace('/[¿?¡!.,;:"\'()]/u', ' ', $pregunta);
$palabras = array_values(array_filter(
    preg_split('/\s+/', trim($textoLimpio)),
    fn($p) => mb_strlen($p) >=2
));
$intencion = detectarIntencionIA($pregunta);

// 1. Contexto de libros (todos los roles)
$contexto = '';

switch ($intencion) {

    case 'libros':

        $libros = buscarLibrosRelevantes($pdo, $palabras);
        $contexto = construirContextoLibros($libros);

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

    default:

        // Para preguntas generales también buscamos libros por si acaso.
        $libros = buscarLibrosRelevantes($pdo, $palabras);

        if (!empty($libros)) {
            $contexto = construirContextoLibros($libros);
        }

        break;
}

// 3. Armar el prompt final (con historial reciente) y llamar a Ollama
if (!isset($_SESSION['historial_chat'])) {
    $_SESSION['historial_chat'] = [];
}

$promptFinal = construirPrompt($contexto, $pregunta, $usuarioActual, $esAdmin, $_SESSION['historial_chat']);
$respuesta = preguntarOllama($promptFinal);

// Guardamos el intercambio en el historial (limitado a los últimos 6 mensajes = 3 turnos)
$_SESSION['historial_chat'][] = ['rol' => 'usuario', 'texto' => $pregunta];
$_SESSION['historial_chat'][] = ['rol' => 'asistente', 'texto' => $respuesta];
$_SESSION['historial_chat'] = array_slice($_SESSION['historial_chat'], -4);

echo json_encode(['respuesta' => $respuesta]);


// ============================================================
// FUNCIONES
// ============================================================

/**
 * Busca libros relacionados a la pregunta (por título, autor, tema, categoría, editorial, descripción).
 * Solo libros activos y no eliminados.
 */
function buscarLibrosRelevantes(PDO $pdo, array $palabras): array
{
    if (empty($palabras)) {
        return [];
    }

    // Prioridad de búsqueda
    $prioridades = [
        ['titulo', 'temas', 'categoria'],
        ['autor', 'editorial'],
        ['descripcion']
    ];

    foreach ($prioridades as $camposBusqueda) {

        $condiciones = [];
        $params = [];

        foreach ($palabras as $i => $palabra) {

            $condCampos = [];

            foreach ($camposBusqueda as $campo) {

                $key = ":p{$i}_{$campo}";

                $condCampos[] = "$campo LIKE $key";

                $params[$key] = "%{$palabra}%";
            }

            $condiciones[] = "(" . implode(" OR ", $condCampos) . ")";
        }

        $sql = "
            SELECT
                titulo,
                autor,
                idioma,
                disponibilidad,
                ejemplares,
                prestados,
                temas,
                categoria,
                tipo,
                editorial,
                ano
            FROM tb_libros
            WHERE estado='1'
              AND fyh_eliminacion IS NULL
              AND (" . implode(" OR ", $condiciones) . ")
            LIMIT 10
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si encontró resultados en esta prioridad, ya no sigue buscando.
        if (!empty($resultados)) {
            return $resultados;
        }
    }

    // No encontró nada
    return [];
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
estadisticas
usuarios
saludo
social
general

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