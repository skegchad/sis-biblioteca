<?php
/**
 * ollama.php
 *
 * Capa de conexión con Ollama para el asistente de biblioteca (BMO).
 *
 * Decisiones de diseño:
 * - Usa /api/chat (no /api/generate): Ollama arma el chat template oficial
 *   del modelo turno por turno. Un modelo pequeño (3B-4B) es mucho más
 *   fiable respondiendo cuando recibe roles reales (system/user/assistant)
 *   que cuando recibe un solo bloque de texto plano con secciones tipo
 *   "CONTEXTO:", "HISTORIAL:", etc.
 * - Usa "format" con JSON Schema para el clasificador de intención, en vez
 *   de pedirle "responde solo JSON" y luego parsear con regex. Ollama
 *   restringe la generación token a token para que el JSON sea válido y
 *   cumpla el esquema (enums incluidos), lo que elimina casi por completo
 *   los fallos de parseo.
 * - Usa "keep_alive" para mantener el modelo cargado en RAM entre
 *   peticiones. En una Raspberry Pi, recargar el modelo desde disco en
 *   cada mensaje puede costar varios segundos; con keep_alive activo esa
 *   carga solo ocurre una vez.
 */

/** Excepción específica para poder distinguir fallos de Ollama del resto de errores. */
class OllamaException extends RuntimeException
{
}

// --- Configuración por defecto (ajustable sin tocar el resto del código) ---

if (!defined('OLLAMA_URL')) {
    define('OLLAMA_URL', 'http://127.0.0.1:11434');
}

if (!defined('OLLAMA_MODEL')) {
    // Respeta $modelo_ia si ya viene definido en config.php; si no, usa un default razonable.
    define('OLLAMA_MODEL', $GLOBALS['modelo_ia'] ?? 'gemma3n:e2b');
}

if (!defined('OLLAMA_TIMEOUT')) {
    define('OLLAMA_TIMEOUT', 120); // segundos
}

if (!defined('OLLAMA_KEEP_ALIVE')) {
    // Mantiene el modelo cargado en RAM 30 min desde la última petición.
    // Usa '-1' si quieres que nunca se descargue mientras la Pi esté encendida.
    define('OLLAMA_KEEP_ALIVE', '30m');
}

/**
 * Llamada de bajo nivel a /api/chat. Devuelve el texto de la respuesta.
 *
 * @param array       $messages    Mensajes con forma [['role'=>'system|user|assistant','content'=>'...'], ...]
 * @param array       $options     Opciones de Ollama (temperature, num_predict, num_ctx, etc.)
 * @param string|null $modelo      Modelo a usar; por defecto OLLAMA_MODEL.
 * @param array|null  $format      JSON Schema (array asociativo) para forzar salida estructurada. Null = texto libre.
 *
 * @throws OllamaException Si falla la conexión o Ollama devuelve un error.
 */
function ollamaChat(
    array $messages,
    array $options = [],
    ?string $modelo = null,
    ?array $format = null
): string {
    $payload = [
        'model'      => $modelo ?? OLLAMA_MODEL,
        'messages'   => $messages,
        'stream'     => false,
        'think'      => false,
        'keep_alive' => OLLAMA_KEEP_ALIVE,
        'options'    => $options,
    ];

    if ($format !== null) {
        $payload['format'] = $format;
    }

    $ch = curl_init(OLLAMA_URL . '/api/chat');

    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => OLLAMA_TIMEOUT,
    ]);

    $respuestaCruda = curl_exec($ch);

    if ($respuestaCruda === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new OllamaException("No se pudo conectar con Ollama: $error");
    }

    curl_close($ch);

    $result = json_decode($respuestaCruda, true);

    if ($result === null) {
        throw new OllamaException(
            'Ollama devolvió una respuesta que no es JSON válido: ' . substr($respuestaCruda, 0, 300)
        );
    }

    if (isset($result['error'])) {
        throw new OllamaException('Error de Ollama: ' . $result['error']);
    }

    $texto = $result['message']['content'] ?? '';

    // Red de seguridad por si algún modelo con "thinking" cuela un bloque
    // de razonamiento aunque think=false esté activo.
    $texto = preg_replace('/<think>.*?<\/think>/s', '', $texto);

    return trim($texto);
}

/**
 * Variante para respuestas estructuradas (JSON Schema). Ideal para el
 * clasificador de intención, donde necesitamos un objeto con forma fija.
 *
 * @throws OllamaException Si Ollama falla o el JSON no se puede decodificar.
 */
function ollamaChatJSON(
    array $messages,
    array $schema,
    ?string $modelo = null,
    float $temperature = 0.0,
    int $numPredict = 300
): array {
    $texto = ollamaChat(
        $messages,
        [
            'temperature'  => $temperature,
            'num_predict'  => $numPredict,
            // Contexto acotado: suficiente para instrucciones + historial corto,
            // sin desperdiciar RAM/tiempo en una Pi.
            'num_ctx'      => 4096,
        ],
        $modelo,
        $schema
    );

    $json = json_decode($texto, true);

    if (!is_array($json)) {
        throw new OllamaException('El modelo no devolvió un JSON parseable: ' . substr($texto, 0, 300));
    }

    return $json;
}