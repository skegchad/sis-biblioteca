<?php
// ollama.php - Función para consultar Ollama
function preguntarOllama(
    string $prompt,
    float $temperature = 0.2,
    ?string $modelo = null
): string
{
    global $modelo_ia;

    if ($modelo === null) {
        $modelo = $modelo_ia;
    }
    $url = 'http://127.0.0.1:11434/api/generate';

    $data = [
        'model' => $modelo,
        'prompt' => $prompt,
        'stream' => false,
        'options' => [
            'temperature' => $temperature
        ]
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "Error: $error";
    }

    curl_close($ch);

    $result = json_decode($response, true);

    if ($result === null) {
        return "Error JSON: " . json_last_error_msg() . "\nRespuesta recibida: " . $response;
    }

    if (isset($result['error'])) {
        return "Error Ollama: " . $result['error'];
    }

    return trim($result['response'] ?? 'Sin respuesta');
    
}