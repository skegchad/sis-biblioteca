<?php
/**
 * chat_test.php - Endpoint del asistente de biblioteca (BMO)
 *
 * Diseño pensado para correr rápido y con pocos recursos (Raspberry Pi):
 *
 * 1. UNA llamada al modelo para clasificar intención (analizarConsultaIA),
 *    con JSON garantizado por schema.
 *
 * 2. CERO llamadas al modelo para respuestas sobre libros (búsqueda,
 *    detalle, disponibilidad, ubicación, similares...). Todas se arman con
 *    plantillas PHP usando datos reales de MySQL, para eliminar por
 *    completo el riesgo de que el asistente invente libros.
 *
 * 3. UNA llamada adicional al modelo, SOLO cuando la intención es
 *    "general" (saludo, charla casual, preguntas sobre el propio
 *    asistente, o cualquier cosa fuera de catálogo). Esa llamada no tiene
 *    acceso a la base de datos ni a un schema JSON: responde en texto
 *    libre, con instrucciones explícitas de no inventar datos de libros
 *    ni de la biblioteca. Así el chat se siente natural en lo social sin
 *    arriesgar la precisión de los datos del catálogo.
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

header('Content-Type: application/json; charset=utf-8');

require_once '../app/config/config.php';
require_once '../app/config/conexion.php';
require_once 'ollama.php';

// --- Debug opcional: pon esto en false antes de pasar a producción ---
const CHAT_DEBUG = true;

function debugLog(string $etiqueta, $data): void
{
    if (!CHAT_DEBUG) {
        return;
    }
    file_put_contents(
        __DIR__ . '/chat_debug.log',
        '[' . date('Y-m-d H:i:s') . "] $etiqueta:\n" . print_r($data, true) . "\n\n",
        FILE_APPEND
    );
}

// --- Sesión ---
session_start();

// Utilidad de prueba: abre chat_test.php?reset=1 para limpiar la conversación.
if (isset($_GET['reset'])) {
    unset($_SESSION['historial_chat'], $_SESSION['contexto_libros'], $_SESSION['ultimas_respuestas']);
    echo json_encode(['respuesta' => 'Contexto reiniciado.']);
    exit;
}

if (!isset($_SESSION['sesion_user'])) {
    http_response_code(401);
    echo json_encode(['respuesta' => 'Debes iniciar sesión para usar el chat.']);
    exit;
}

// --- Entrada del usuario ---
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
$pregunta = trim($input['mensaje'] ?? '');

if ($pregunta === '') {
    echo json_encode(['respuesta' => 'Por favor escribe una pregunta.']);
    exit;
}

try {
    $respuesta = procesarMensaje($pdo, $pregunta);
} catch (OllamaException $e) {
    debugLog('OllamaException', $e->getMessage());
    http_response_code(503);
    $respuesta = 'No pude conectarme al modelo de IA en este momento. Intenta de nuevo en unos segundos.';
    echo json_encode(['respuesta' => $respuesta]);
    exit;
}

echo json_encode(['respuesta' => $respuesta], JSON_UNESCAPED_UNICODE);

// ============================================================
// FLUJO PRINCIPAL
// ============================================================

function procesarMensaje(PDO $pdo, string $pregunta): string
{
    if (!isset($_SESSION['historial_chat'])) {
        $_SESSION['historial_chat'] = [];
    }

    $analisis = analizarConsultaIA($pregunta, $_SESSION['historial_chat']);
    debugLog('analisis', $analisis);

    $intencion       = $analisis['intencion'];
    $accion          = $analisis['accion'];
    $seguimiento     = $analisis['seguimiento'];
    $indice          = (int)($analisis['indice'] ?? 0);
    $filtrosBusqueda = $analisis['filtros'] ?? [];

    $ultimoLibro = resolverLibroActual($pregunta, $seguimiento, $indice);

    $respuestaDirecta = null;
    $sinResultadosLibros = false;
    $sinRecomendaciones = false;

    // Acciones que necesitan un libro "activo" en contexto para responder.
    $accionesRequierenLibro = [
        'descripcion', 'autor', 'disponibilidad', 'ubicacion', 'idioma',
        'ano', 'editorial', 'categoria', 'subcategoria', 'tipo', 'temas', 'edicion',
    ];

    if (in_array($accion, $accionesRequierenLibro, true) && $ultimoLibro === null) {
        $respuestaDirecta = 'Todavía no hemos hablado de ningún libro en concreto. '
            . 'Cuéntame qué título, autor o tema buscas y te ayudo a encontrarlo. 😊';
    }

    if ($respuestaDirecta === null) {
        switch ($accion) {

            case 'buscar':
                $libros = buscarLibrosRelevantes($pdo, $filtrosBusqueda);

                if (empty($libros)) {
                    $sinResultadosLibros = true;
                } else {
                    guardarContextoLibros($pregunta, $libros);
                    $respuestaDirecta = count($libros) === 1
                        ? formatearDetalleLibro($libros[0])
                        : formatearListaLibros($libros);
                }
                break;

            case 'mostrar':
                if ($indice > 0 && isset($_SESSION['contexto_libros']['libros'][$indice - 1])) {
                    $respuestaDirecta = formatearDetalleLibro($_SESSION['contexto_libros']['libros'][$indice - 1]);
                } elseif (isset($_SESSION['contexto_libros']['libros'])) {
                    $respuestaDirecta = formatearListaLibros($_SESSION['contexto_libros']['libros']);
                } else {
                    $respuestaDirecta = 'Todavía no he buscado ningún libro. '
                        . "Pídeme, por ejemplo: \"libros de García Márquez\" o \"libros sobre historia\".";
                }
                break;

            case 'descripcion':
                $respuestaDirecta = !empty($ultimoLibro['descripcion'])
                    ? "📖 \"{$ultimoLibro['titulo']}\"\n\n{$ultimoLibro['descripcion']}"
                    : "No tengo una descripción registrada para \"{$ultimoLibro['titulo']}\".";
                break;

            case 'autor':
                $respuestaDirecta = "El autor de \"{$ultimoLibro['titulo']}\" es {$ultimoLibro['autor']}.";
                break;

            case 'idioma':
                $respuestaDirecta = "\"{$ultimoLibro['titulo']}\" está escrito en {$ultimoLibro['idioma']}.";
                break;

            case 'ano':
                $respuestaDirecta = "\"{$ultimoLibro['titulo']}\" fue publicado en {$ultimoLibro['ano']}.";
                break;

            case 'editorial':
                $respuestaDirecta = "\"{$ultimoLibro['titulo']}\" fue publicado por {$ultimoLibro['editorial']}.";
                break;

            case 'categoria':
                $respuestaDirecta = "\"{$ultimoLibro['titulo']}\" pertenece a la categoría {$ultimoLibro['categoria']}.";
                break;

            case 'subcategoria':
                $respuestaDirecta = !empty($ultimoLibro['subcategoria'])
                    ? "\"{$ultimoLibro['titulo']}\" pertenece a la subcategoría {$ultimoLibro['subcategoria']}."
                    : "No hay una subcategoría registrada para \"{$ultimoLibro['titulo']}\".";
                break;

            case 'tipo':
                $respuestaDirecta = "\"{$ultimoLibro['titulo']}\" es de tipo {$ultimoLibro['tipo']}.";
                break;

            case 'temas':
                $respuestaDirecta = !empty($ultimoLibro['temas'])
                    ? "\"{$ultimoLibro['titulo']}\" trata sobre: {$ultimoLibro['temas']}."
                    : "No hay temas registrados para \"{$ultimoLibro['titulo']}\".";
                break;

            case 'edicion':
                $respuestaDirecta = !empty($ultimoLibro['edicion'])
                    ? "La edición de \"{$ultimoLibro['titulo']}\" es: {$ultimoLibro['edicion']}."
                    : "No hay una edición registrada para \"{$ultimoLibro['titulo']}\".";
                break;

            case 'disponibilidad':
                $disponibles = max(0, (int)$ultimoLibro['ejemplares'] - (int)$ultimoLibro['prestados']);
                $respuestaDirecta = $disponibles > 0
                    ? "Sí, \"{$ultimoLibro['titulo']}\" está disponible. Hay {$disponibles} ejemplar" . ($disponibles === 1 ? '' : 'es') . " libre" . ($disponibles === 1 ? '' : 's') . "."
                    : "En este momento todos los ejemplares de \"{$ultimoLibro['titulo']}\" están prestados.";
                break;

            case 'ubicacion':
                $respuestaDirecta = "Puedes encontrar \"{$ultimoLibro['titulo']}\" en la sección {$ultimoLibro['seccion']}, "
                    . "bloque {$ultimoLibro['bloque']}, código CDD {$ultimoLibro['cdd']}.";
                break;

            case 'similares':
                if ($ultimoLibro === null) {
                    $respuestaDirecta = '¿Sobre cuál libro quieres que te recomiende algo parecido? '
                        . 'Dime el título y busco opciones similares.';
                    break;
                }

                $recomendados = buscarLibrosSimilares($pdo, $ultimoLibro);

                if (empty($recomendados)) {
                    $sinRecomendaciones = true;
                } else {
                    guardarContextoLibros($pregunta, $recomendados);
                    $respuestaDirecta = formatearListaLibros($recomendados);
                }
                break;

            // 'ninguna' y cualquier otro valor no reconocido caen al bloque
            // de intención "general" más abajo (saludo, social, identidad
            // y todo lo que no sea consulta de catálogo ya viven ahí).
        }
    }

    if ($sinRecomendaciones) {
        return 'No encontré otros libros parecidos a ese en nuestra biblioteca. '
            . '¿Quieres intentar con otro libro o pedirme recomendaciones sobre otro tema?';
    }

    if ($sinResultadosLibros) {
        return 'No encontré ningún libro que coincida con esa búsqueda en nuestra base de datos. '
            . '¿Quieres intentar con otro título, autor o tema?';
    }

    if ($respuestaDirecta !== null) {
        guardarTurnoEnHistorial($pregunta, $respuestaDirecta);
        return $respuestaDirecta;
    }

    // Nada de lo anterior aplicó: intención "general" (saludo, charla
    // casual, preguntas sobre el asistente, o cualquier otra cosa fuera
    // de catálogo). La maneja la IA en texto libre, sin acceso a datos
    // de libros reales, para que suene natural.
    $respuesta = respuestaGeneral($pregunta, $_SESSION['historial_chat']);

    guardarTurnoEnHistorial($pregunta, $respuesta);
    return $respuesta;
}

/**
 * Decide cuál es el "libro activo" para preguntas de seguimiento, usando
 * (en este orden): número/posición mencionado ("el segundo", "el 2"),
 * título mencionado explícitamente, o el último libro activo en sesión.
 */
function resolverLibroActual(string $pregunta, bool $seguimiento, int $indice): ?array
{
    if (!$seguimiento || !isset($_SESSION['contexto_libros']['libros'])) {
        return $_SESSION['contexto_libros']['ultimo'] ?? null;
    }

    $libros = $_SESSION['contexto_libros']['libros'];
    $resuelto = null;

    if ($indice > 0 && isset($libros[$indice - 1])) {
        $resuelto = $libros[$indice - 1];
    }

    if ($resuelto === null) {
        foreach ($libros as $libro) {
            if (mb_stripos($pregunta, $libro['titulo']) !== false) {
                $resuelto = $libro;
                break;
            }
        }
    }

    if ($resuelto === null) {
        $resuelto = $_SESSION['contexto_libros']['ultimo'] ?? null;
    } else {
        $_SESSION['contexto_libros']['ultimo'] = $resuelto;
    }

    return $resuelto;
}

function guardarContextoLibros(string $pregunta, array $libros): void
{
    $_SESSION['contexto_libros'] = [
        'consulta' => $pregunta,
        'libros'   => $libros,
        'ultimo'   => $libros[0],
    ];
}

function guardarTurnoEnHistorial(string $pregunta, string $respuesta): void
{
    $_SESSION['historial_chat'][] = ['rol' => 'usuario', 'texto' => $pregunta];
    $_SESSION['historial_chat'][] = ['rol' => 'asistente', 'texto' => $respuesta];
    // Limitado a los últimos 3 turnos (6 mensajes) para no inflar el prompt.
    $_SESSION['historial_chat'] = array_slice($_SESSION['historial_chat'], -6);
}

// ============================================================
// BÚSQUEDA EN LA BASE DE DATOS
// ============================================================

function buscarLibrosRelevantes(PDO $pdo, array $filtros): array
{
    if (empty($filtros)) {
        return [];
    }

    $camposPermitidos = [
        'titulo', 'autor', 'temas', 'tipo', 'categoria', 'subcategoria',
        'idioma', 'editorial', 'edicion', 'ano', 'cdd', 'bloque', 'seccion',
    ];

    $condiciones = [];
    $params = [];

    foreach ($filtros as $i => $filtro) {
        $campo = $filtro['campo'] ?? '';
        $valor = $filtro['valor'] ?? '';

        if (!in_array($campo, $camposPermitidos, true) || $valor === '') {
            continue;
        }

        $key = ":f$i";

        // "categoria" y "temas" se traslapan conceptualmente (un tema como
        // "programación" puede estar cargado en cualquiera de los dos
        // campos según el libro), así que buscamos en ambos sin importar
        // cuál haya elegido el clasificador. Así no dependemos de que la
        // IA adivine el campo exacto.
        if (in_array($campo, ['categoria', 'temas'], true)) {
            $condiciones[] = "(l.categoria LIKE $key OR l.temas LIKE $key)";
        } else {
            $condiciones[] = "l.$campo LIKE $key";
        }

        $params[$key] = "%$valor%";
    }

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
        WHERE l.estado = '1'
          AND l.fyh_eliminacion IS NULL
          AND (" . implode(' AND ', $condiciones) . ")
        LIMIT 10
    ";

    debugLog('sql_buscar', ['sql' => $sql, 'params' => $params]);

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function buscarLibrosSimilares(PDO $pdo, array $libro): array
{
    $temas = array_filter(array_map('trim', explode(',', $libro['temas'] ?? '')));

    if (empty($temas)) {
        return [];
    }

    $condiciones = [];
    $params = [];

    foreach ($temas as $i => $tema) {
        $key = ":tema$i";
        $condiciones[] = "temas LIKE $key";
        $params[$key] = "%$tema%";
    }

    $params[':id'] = $libro['id_libro'];

    $sql = "
        SELECT
            id_libro, titulo, descripcion, autor, idioma, disponibilidad,
            temas, tipo, edicion, ano, cdd, bloque, categoria, subcategoria,
            seccion, editorial, ejemplares, prestados
        FROM tb_libros
        WHERE estado = '1'
          AND fyh_eliminacion IS NULL
          AND id_libro <> :id
          AND (" . implode(' OR ', $condiciones) . ")
        LIMIT 5
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================
// PLANTILLAS DE RESPUESTA PARA LIBROS (sin IA, 100% datos reales de MySQL)
// ============================================================

function formatearListaLibros(array $libros): string
{
    $lineas = ['¡Encontré ' . count($libros) . ' libros que coinciden! 😊', ''];

    foreach ($libros as $i => $libro) {
        $disponibles = max(0, (int)$libro['ejemplares'] - (int)$libro['prestados']);
        $estado = $disponibles > 0 ? "disponible ({$disponibles} libre" . ($disponibles === 1 ? '' : 's') . ')' : 'prestado';

        $num = $i + 1;
        $lineas[] = "{$num}. 📖 \"{$libro['titulo']}\" — {$libro['autor']} · {$estado}";
    }

    $lineas[] = '';
    $lineas[] = '¿Sobre cuál quieres saber más? Dime el número o el título.';

    return implode("\n", $lineas);
}

function formatearDetalleLibro(array $libro): string
{
    $disponibles = max(0, (int)$libro['ejemplares'] - (int)$libro['prestados']);
    $disponibilidadTexto = $disponibles > 0
        ? "Ahora mismo está disponible para préstamo, con {$disponibles} ejemplar" . ($disponibles === 1 ? '' : 'es') . " libre" . ($disponibles === 1 ? '' : 's') . '.'
        : 'En este momento no está disponible, todos sus ejemplares están prestados.';

    $categoriaTexto = $libro['categoria'];
    if (!empty($libro['subcategoria'])) {
        $categoriaTexto .= " ({$libro['subcategoria']})";
    }

    $parrafo = "📖 \"{$libro['titulo']}\", de {$libro['autor']}, pertenece a la categoría {$categoriaTexto}";
    if (!empty($libro['temas'])) {
        $parrafo .= " y trata sobre {$libro['temas']}";
    }
    $parrafo .= '. ';

    $parrafo .= "Fue publicado por {$libro['editorial']}";
    if (!empty($libro['edicion'])) {
        $parrafo .= " (edición {$libro['edicion']})";
    }
    $parrafo .= " en {$libro['ano']}, está en {$libro['idioma']} y es de tipo {$libro['tipo']}. ";

    if (!empty($libro['seccion']) || !empty($libro['bloque']) || !empty($libro['cdd'])) {
        $parrafo .= "Lo puedes encontrar en la sección {$libro['seccion']}, bloque {$libro['bloque']}, código CDD {$libro['cdd']}. ";
    }

    $parrafo .= $disponibilidadTexto;

    if (!empty($libro['descripcion'])) {
        $parrafo .= "\n\n{$libro['descripcion']}";
    }

    return $parrafo;
}

// ============================================================
// RESPUESTA "GENERAL" (saludo, social, identidad y todo lo demás):
// la maneja la IA en texto libre, sin datos de libros ni schema JSON.
// ============================================================

/**
 * Segunda llamada al modelo, SOLO para la intención "general". A
 * diferencia del clasificador, aquí no se pide JSON: se pide una
 * respuesta conversacional corta en texto plano.
 *
 * Usa ollamaChat() en modo texto libre (sin $format), con temperatura más
 * alta que el clasificador porque aquí sí buscamos variedad natural en el
 * tono, no determinismo.
 */
function respuestaGeneral(string $pregunta, array $historial): string
{
    $system = construirSystemPromptGeneral();

    $messages = [['role' => 'system', 'content' => $system]];

    foreach ($historial as $mensaje) {
        $messages[] = [
            'role'    => $mensaje['rol'] === 'usuario' ? 'user' : 'assistant',
            'content' => $mensaje['texto'],
        ];
    }

    $messages[] = ['role' => 'user', 'content' => $pregunta];

    try {
        $texto = ollamaChat(
            $messages,
            [
                'temperature' => 0.7,
                'num_predict' => 200,
                'num_ctx'     => 4096,
            ]
            // $modelo y $format se dejan en null: mismo modelo por defecto,
            // sin JSON schema (texto libre).
        );
    } catch (OllamaException $e) {
        debugLog('OllamaException en respuestaGeneral', $e->getMessage());
        // Red de seguridad si el modelo falla: no rompemos el chat.
        return 'Ahora mismo no puedo responder eso, pero puedo ayudarte a '
            . 'buscar libros por título, autor o tema. ¿Qué buscas?';
    }

    $texto = trim($texto);

    return $texto !== ''
        ? $texto
        : 'No estoy seguro de haber entendido eso. ¿Buscas algún libro, autor o tema en particular?';
}

function construirSystemPromptGeneral(): string
{
    return <<<PROMPT
Eres BMO 🤖, el asistente conversacional de una biblioteca.

El mensaje del usuario que vas a responder YA fue filtrado antes de
llegar aquí: no es una búsqueda de libros ni una petición de
recomendaciones (eso lo maneja otra parte del sistema con datos reales).
Lo que te llega es todo lo demás: saludos, charla casual, preguntas sobre
ti mismo, agradecimientos, o cualquier tema fuera del catálogo.

Reglas importantes:
- Responde de forma natural, cálida y breve (1-3 frases), en español, con
  el mismo tono cercano usado en el resto del chat. Puedes usar algún
  emoji ocasional, sin abusar.
- Te llamas BMO y eres el asistente virtual de esta biblioteca. Tu función
  es ayudar a buscar libros por título, autor, tema o categoría, revisar
  disponibilidad y ubicación, y recomendar libros similares a uno que ya
  le haya gustado al usuario.
- NUNCA inventes títulos, autores, datos de disponibilidad, ubicación ni
  ningún otro dato de libros concretos: no tienes acceso al catálogo en
  este mensaje. Si la conversación se acerca a pedir un libro específico,
  invita al usuario a decirte el título, autor o tema para buscarlo de
  verdad.
- NUNCA inventes información sobre la biblioteca física (horarios,
  políticas de préstamo, ubicación del edificio, etc.) que no se te haya
  dado explícitamente. Si te preguntan eso, di amablemente que no tienes
  esa información a la mano.
- No repitas literalmente el mismo saludo o la misma frase que usaste en
  turnos anteriores del historial; varía la redacción de forma natural.
PROMPT;
}

// ============================================================
// CLASIFICADOR DE INTENCIÓN (única llamada al modelo por turno)
// ============================================================

function analizarConsultaIA(string $pregunta, array $historial): array
{
    $schema = [
        'type' => 'object',
        'properties' => [
            'intencion' => [
                'type' => 'string',
                'enum' => ['libros', 'similares', 'general'],
            ],
            'seguimiento' => ['type' => 'boolean'],
            'accion' => [
                'type' => 'string',
                'enum' => [
                    'buscar', 'mostrar', 'descripcion', 'autor', 'disponibilidad',
                    'ubicacion', 'idioma', 'ano', 'editorial', 'categoria',
                    'subcategoria', 'tipo', 'temas', 'edicion', 'similares', 'ninguna',
                ],
            ],
            'indice' => [
                'type' => 'integer',
                'description' => 'Posición (1 = primero, 2 = segundo...) del libro de la última lista al que se refiere el usuario. 0 si no aplica.',
            ],
            'filtros' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'campo' => [
                            'type' => 'string',
                            'enum' => [
                                'titulo', 'autor', 'temas', 'tipo', 'categoria',
                                'subcategoria', 'idioma', 'editorial', 'edicion',
                                'ano', 'cdd', 'bloque', 'seccion',
                            ],
                        ],
                        'valor' => ['type' => 'string'],
                    ],
                    'required' => ['campo', 'valor'],
                ],
            ],
        ],
        'required' => ['intencion', 'seguimiento', 'accion', 'indice', 'filtros'],
    ];

    $system = construirSystemPromptClasificador();

    $messages = [['role' => 'system', 'content' => $system]];

    // Historial reciente como turnos reales (no como texto embebido).
    foreach ($historial as $mensaje) {
        $messages[] = [
            'role'    => $mensaje['rol'] === 'usuario' ? 'user' : 'assistant',
            'content' => $mensaje['texto'],
        ];
    }

    $messages[] = ['role' => 'user', 'content' => $pregunta];

    $default = [
        'intencion' => 'general', 'seguimiento' => false,
        'accion' => 'ninguna', 'indice' => 0, 'filtros' => [],
    ];

    try {
        // temperature=0 (antes 0.1): para un clasificador estructurado no
        // aporta nada tener algo de aleatoriedad, y así el resultado es
        // reproducible cuando necesites depurar con chat_debug.log.
        $json = ollamaChatJSON($messages, $schema, null, 0.0);
    } catch (OllamaException $e) {
        debugLog('OllamaException en clasificador', $e->getMessage());
        return $default;
    }

    return $json + $default;
}

function construirSystemPromptClasificador(): string
{
    return <<<PROMPT
Eres el clasificador de intención de BMO, el asistente de una biblioteca.

Tu única función es analizar el ÚLTIMO mensaje del usuario (considerando el
historial de la conversación como contexto) y devolver un JSON con la
intención detectada. No respondas al usuario, no expliques nada.

=========================
INTENCIONES
=========================
- libros: preguntas sobre libros, autores, títulos, categorías, disponibilidad, etc.
- similares: pide libros parecidos o recomendaciones.
- general: CUALQUIER OTRA COSA que no sea una búsqueda de libros ni una
  petición de recomendaciones. Esto incluye saludos (hola, buenos días),
  charla casual (cómo estás, gracias, jaja), preguntas sobre el propio
  asistente (su nombre, quién es, si se llama BMO, qué puede hacer), y
  cualquier otro tema que no encaje en "libros" o "similares".

=========================
ACCIONES
=========================
buscar, mostrar, descripcion, autor, disponibilidad, ubicacion, idioma, ano,
editorial, categoria, subcategoria, tipo, temas, edicion, similares, ninguna.

Para intencion="general", accion siempre es "ninguna".

=========================
REGLA CLAVE: CUALQUIER FORMA DE PEDIR LIBROS ES accion="buscar"
=========================
No importa si la pregunta es afirmativa, negativa o interrogativa; si el
usuario está pidiendo o preguntando por libros que existen en el catálogo
(nuevos, no el libro que ya se venía hablando), es accion="buscar" con
seguimiento=false.

Ejemplos (todos deben dar accion="buscar"):

Usuario: ¿Qué libros de Kafka tienes?
{"intencion":"libros","seguimiento":false,"accion":"buscar","indice":0,"filtros":[{"campo":"autor","valor":"Kafka"}]}

Usuario: ¿Tienes algún libro de Nietzsche?
{"intencion":"libros","seguimiento":false,"accion":"buscar","indice":0,"filtros":[{"campo":"autor","valor":"Nietzsche"}]}

Usuario: ¿Tienen libros sobre historia?
{"intencion":"libros","seguimiento":false,"accion":"buscar","indice":0,"filtros":[{"campo":"temas","valor":"historia"}]}

Usuario: ¿Hay algo de García Márquez?
{"intencion":"libros","seguimiento":false,"accion":"buscar","indice":0,"filtros":[{"campo":"autor","valor":"García Márquez"}]}

Usuario: ¿Cuentan con novelas de terror?
{"intencion":"libros","seguimiento":false,"accion":"buscar","indice":0,"filtros":[{"campo":"tipo","valor":"Novela"},{"campo":"temas","valor":"terror"}]}

Usuario: Busca libros de filosofía
{"intencion":"libros","seguimiento":false,"accion":"buscar","indice":0,"filtros":[{"campo":"categoria","valor":"Filosofía"}]}

=========================
TÍTULOS ESPECÍFICOS DE LIBROS
=========================
Si el usuario menciona el título de una obra concreta (con o sin comillas,
o pregunta "¿Tienes <título>?"), usa campo="titulo" con ese valor exacto.
Esto tiene PRIORIDAD sobre cualquier tema, autor o categoría mencionados en
turnos anteriores de la conversación: NO reutilices el filtro de un mensaje
anterior solo porque el tema general se parece.

Usuario: ¿Tienes "El nombre de la rosa"?
{"intencion":"libros","seguimiento":false,"accion":"buscar","indice":0,"filtros":[{"campo":"titulo","valor":"El nombre de la rosa"}]}

Usuario: ¿Tienes libros sobre programación?
{"intencion":"libros","seguimiento":false,"accion":"buscar","indice":0,"filtros":[{"campo":"temas","valor":"programación"}]}

Usuario: ¿Tienes "The C Programming Language"?
{"intencion":"libros","seguimiento":false,"accion":"buscar","indice":0,"filtros":[{"campo":"titulo","valor":"The C Programming Language"}]}

=========================
CADA MENSAJE SE ANALIZA POR SU CUENTA
=========================
Aunque el historial reciente muestre otra búsqueda con un filtro parecido,
arma los filtros solo con lo que dice el mensaje ACTUAL. Nunca copies un
filtro de un turno anterior salvo que sea un verdadero seguimiento sobre
ESE MISMO resultado (ver más abajo).

=========================
CATEGORÍA VS. TEMAS
=========================
"categoria" es una clasificación amplia y fija del catálogo (ej. Filosofía,
Literatura). "temas" es una lista libre de conceptos específicos de cada
libro (ej. Identidad, Soledad, programación, historia). La búsqueda revisa
ambos campos a la vez, así que no es crítico acertar cuál de los dos es
—pero si no estás seguro de que sea una categoría reconocida del catálogo,
usa campo="temas" por default.

=========================
SEGUIMIENTO: preguntas sobre EL LIBRO ya mostrado
=========================
Cuando el usuario usa "su", "ese", "él", "este libro", o confirma con
"sí"/"claro"/"cuéntame" justo después de que la lista de libros ya fue
mostrada, es seguimiento=true. Usa "accion" según lo que pregunte
específicamente; si solo confirma sin pedir un dato concreto, usa
accion="mostrar".

Usuario: ¿Quién lo escribió?
{"intencion":"libros","seguimiento":true,"accion":"autor","indice":0,"filtros":[]}

Usuario: ¿Está disponible?
{"intencion":"libros","seguimiento":true,"accion":"disponibilidad","indice":0,"filtros":[]}

Usuario: ¿Dónde está ubicado?
{"intencion":"libros","seguimiento":true,"accion":"ubicacion","indice":0,"filtros":[]}

Usuario: ¿En qué sección está?
{"intencion":"libros","seguimiento":true,"accion":"ubicacion","indice":0,"filtros":[]}

Usuario: ¿A qué CDD o bloque pertenece?
{"intencion":"libros","seguimiento":true,"accion":"ubicacion","indice":0,"filtros":[]}

Nota: accion="ubicacion" cubre CUALQUIER pregunta sobre dónde encontrar
físicamente el libro (sección, bloque, código CDD, "dónde está", etc.),
sin importar cuál de esas palabras use el usuario. No confundir con
accion="mostrar" ni accion="descripcion".

Usuario: Cuéntame más
{"intencion":"libros","seguimiento":true,"accion":"mostrar","indice":0,"filtros":[]}

=========================
"DE QUÉ TRATA" vs. "QUÉ TEMAS TRATA" (¡no es una búsqueda nueva!)
=========================
Cuando ya hay un libro activo en la conversación (se acaba de mostrar su
detalle o su lista), preguntas como "¿de qué trata?", "¿de qué se trata?",
"¿qué temas trata?", "¿sobre qué temas habla?" o "¿cuáles son los temas?"
son SIEMPRE seguimiento=true sobre ESE libro. NUNCA generes
accion="buscar" ni un filtro nuevo para estas preguntas: el usuario no
está pidiendo otro libro, está preguntando por el que ya está en pantalla.

Distingue así:
- Si la pregunta usa la palabra "tema(s)" explícitamente (ej. "¿qué temas
  trata?", "¿sobre qué temas habla?", "¿cuáles son los temas?"), usa
  accion="temas" (devuelve el contenido tal cual de la columna temas del
  libro).
- Si la pregunta es más general, sin mencionar "temas" (ej. "¿de qué
  trata?", "¿de qué se trata este libro?", "cuéntame de qué va"), usa
  accion="descripcion" (el resumen/sinopsis del libro).

Usuario: ¿De qué trata?
{"intencion":"libros","seguimiento":true,"accion":"descripcion","indice":0,"filtros":[]}

Usuario: ¿De qué se trata este libro?
{"intencion":"libros","seguimiento":true,"accion":"descripcion","indice":0,"filtros":[]}

Usuario: ¿Qué temas trata?
{"intencion":"libros","seguimiento":true,"accion":"temas","indice":0,"filtros":[]}

Usuario: ¿Sobre qué temas habla?
{"intencion":"libros","seguimiento":true,"accion":"temas","indice":0,"filtros":[]}

Usuario: ¿Cuáles son los temas que trata?
{"intencion":"libros","seguimiento":true,"accion":"temas","indice":0,"filtros":[]}

=========================
REFERENCIA POR NÚMERO/POSICIÓN
=========================
Si el usuario se refiere a un libro de la última lista por su posición
("el primero", "el segundo", "el número 3", "la 2"), usa seguimiento=true,
accion="mostrar" (o la acción específica si pregunta algo puntual de ese
libro) e indice con la posición (1 = primero, 2 = segundo, etc.).

Usuario: Cuéntame del segundo
{"intencion":"libros","seguimiento":true,"accion":"mostrar","indice":2,"filtros":[]}

Usuario: ¿Quién escribió el número 3?
{"intencion":"libros","seguimiento":true,"accion":"autor","indice":3,"filtros":[]}

=========================
RECOMENDACIONES
=========================
Usuario: Recomiéndame algo parecido
{"intencion":"similares","seguimiento":true,"accion":"similares","indice":0,"filtros":[]}

=========================
INTENCIÓN "GENERAL" (saludo, charla casual, identidad y todo lo demás)
=========================
Todo esto es intencion="general", accion="ninguna". No intentes distinguir
subtipos: ya no existen categorías separadas para saludo, social o
identidad, todo se agrupa aquí.

Usuario: Hola
{"intencion":"general","seguimiento":false,"accion":"ninguna","indice":0,"filtros":[]}

Usuario: ¿Cómo estás?
{"intencion":"general","seguimiento":false,"accion":"ninguna","indice":0,"filtros":[]}

Usuario: ¿Te llamas BMO?
{"intencion":"general","seguimiento":false,"accion":"ninguna","indice":0,"filtros":[]}

Usuario: ¿Quién eres?
{"intencion":"general","seguimiento":false,"accion":"ninguna","indice":0,"filtros":[]}

Usuario: ¿Qué puedes hacer?
{"intencion":"general","seguimiento":false,"accion":"ninguna","indice":0,"filtros":[]}

Usuario: Gracias!
{"intencion":"general","seguimiento":false,"accion":"ninguna","indice":0,"filtros":[]}

Responde solamente el JSON, sin texto adicional.
PROMPT;
}