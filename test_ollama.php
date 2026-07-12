<?php

include("ai/ollama.php");

$respuesta = preguntarOllama("Responde solamente: hola");

echo "<pre>";
var_dump($respuesta);
echo "</pre>";