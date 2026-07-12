<?php

//localmente
defined('BD_SERVIDOR') || define('BD_SERVIDOR','localhost');
defined('BD_USUARIO') || define('BD_USUARIO','root');
defined('BD_PASSWORD') || define('BD_PASSWORD','');
defined('BD_SISTEMA') || define('BD_SISTEMA','bd_sis_biblioteca');


//servidor
/*
define("BD_SERVIDOR","localhost");
define("BD_USUARIO","root");
define("BD_PASSWORD"," ");
define("BD_SISTEMA","bd_sis_biblioteca");
*/

$URL='http://' . $_SERVER['HTTP_HOST'] . '/www.sis_biblioteca.com';
$ROOT    = $_SERVER['DOCUMENT_ROOT'] . "/www.sis_biblioteca.com/";

date_default_timezone_set("America/Guayaquil");
    
$fyh_actual = date("Y-m-d H:i:s");
$estado = 1;
$estado_eliminado=0;
$modelo_ia='qwen2.5-coder:3b-instruct';