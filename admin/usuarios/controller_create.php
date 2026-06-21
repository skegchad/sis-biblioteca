<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");


$nombre_completo = $_POST['Nombres'];
$apellidos = $_POST['Apellidos'];
$cedula = $_POST['Cedula'];
$cargo = $_POST['Cargo'];
$nombre_usuario = $_POST['Nombreusuario'];
$curso   = !empty($_POST['Curso'])   ? $_POST['Curso']   : null;
$paralelo = !empty($_POST['Paralelo']) ? $_POST['Paralelo'] : null;
$password = $_POST['Password'];
$verifyPassword = $_POST['verifyPassword'];

$passwordbd = password_hash($password, PASSWORD_DEFAULT);



if ($_POST['Password'] != $verifyPassword) {
    header("Location: " . $URL . "/admin/usuarios/create.php?error=contrasena");
    exit;
} else {
    $sentencia = $pdo->prepare("INSERT INTO tb_usuarios (nombre_completo, apellidos, cedula, nombre_usuario, password, cargo, fyh_creacion, curso, paralelo, estado)
        VALUES (:nombre_completo, :apellidos, :cedula, :nombre_usuario, :password, :cargo, :fyh_creacion, :curso, :paralelo, :estado)");

    $sentencia->bindParam(':nombre_completo', $nombre_completo);
    $sentencia->bindParam(':apellidos',       $apellidos);
    $sentencia->bindParam(':cedula',          $cedula);
    $sentencia->bindParam(':nombre_usuario',  $nombre_usuario);
    $sentencia->bindParam(':password',        $passwordbd);
    $sentencia->bindParam(':cargo',           $cargo);
    $sentencia->bindParam(':fyh_creacion',    $fyh_actual);
    $sentencia->bindParam(':curso',           $curso);
    $sentencia->bindParam(':paralelo',        $paralelo);
    $sentencia->bindParam(':estado',          $estado);

    if($sentencia->execute()){
        header("Location: " . $URL . "/admin/usuarios?success=registrado");
        exit;
    }else{
        echo "Error";
        exit;
    }
}