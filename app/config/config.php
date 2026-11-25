
<?php

// Base de datos
defined('BD_SERVIDOR') || define('BD_SERVIDOR', 'localhost');
defined('BD_USUARIO') || define('BD_USUARIO', 'root');
defined('BD_PASSWORD') || define('BD_PASSWORD', '');
defined('BD_SISTEMA') || define('BD_SISTEMA', 'bd_sis_biblioteca');

// Detectar protocolo HTTPS (compatible con ngrok)
$protocolo = 'http';

if (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
     strtolower(explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'])[0]) === 'https') ||
    (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
) {
    $protocolo = 'https';
}

// URL principal del proyecto
$URL = $protocolo . '://' . $_SERVER['HTTP_HOST'] . '/www.sis_biblioteca.com';

// Directorio raíz
$ROOT = $_SERVER['DOCUMENT_ROOT'] . '/www.sis_biblioteca.com/';

// Configuración general
date_default_timezone_set('America/Guayaquil');

$fyh_actual = date('Y-m-d H:i:s');
$estado = 1;
$estado_eliminado = 0;
$modelo_ia = 'gemma3n:e2b';

?>
