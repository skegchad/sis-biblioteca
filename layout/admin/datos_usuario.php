<?php
$sesion_actual= $_SESSION['sesion_user'];

$query_usuarios = $pdo->prepare("SELECT * FROM tb_usuarios WHERE nombre_usuario = '$sesion_actual' AND estado = '1' ");
$query_usuarios->execute();

$usuarios = $query_usuarios->fetchAll(PDO::FETCH_ASSOC);

foreach ($usuarios as $usuario){
    $id = $usuario['id_usuario'];
    $nombre = $usuario['nombre_completo'];
    $apellidos = $usuario['apellidos'];
    $nombreusuario = $usuario['nombre_usuario'];
    $cedula = $usuario['cedula'];
    $fecha = $usuario['fyh_nacimiento'];
    $cargo = $usuario['cargo'];
    $rutaFoto = $usuario['foto'];
}
?>