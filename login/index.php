<?php
include ("../app/config/config.php");
include ("../app/config/conexion.php");

include("layout/login1.php");

$error = $_GET['error'] ?? null;

if ($error === 'contrasena') {
    echo '<div class="alert alert-danger">Error en la contraseña</div>';
}elseif ($error === 'usuario') {
    echo '<div class="alert alert-danger">No se pudo encontrar un usuario con ese nombre</div>';
}

include("layout/login2.php");?>