<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");

$id_usuario = $_GET['id'];
$nombre_completo = $_POST['Nombres'];
$apellidos = $_POST['Apellidos'];
$cedula = $_POST['Cedula'];
$cargo = $_POST['Cargo'];
$nombre_usuario = $_POST['Nombreusuario'];
$curso   = !empty($_POST['Curso'])   ? $_POST['Curso']   : null;
$paralelo = !empty($_POST['Paralelo']) ? $_POST['Paralelo'] : null;
$fyh_actualizacion = $fyh_actual;

$sentencia = $pdo->prepare("UPDATE tb_usuarios SET
nombre_completo = :nombre_completo,
apellidos = :apellidos,
cedula = :cedula,
cargo = :cargo,
nombre_usuario = :nombre_usuario,
curso = :curso,
paralelo = :paralelo,
fyh_actualizacion = :fyh_actualizacion WHERE id_usuario = :id_usuario");

$sentencia->bindParam(':nombre_completo', $nombre_completo);
$sentencia->bindParam(':apellidos', $apellidos);
$sentencia->bindParam(':cedula', $cedula);
$sentencia->bindParam(':cargo', $cargo);
$sentencia->bindParam(':nombre_usuario', $nombre_usuario);
$sentencia->bindParam(':curso', $curso);
$sentencia->bindParam(':paralelo', $paralelo);
$sentencia->bindParam(':fyh_actualizacion', $fyh_actualizacion);
$sentencia->bindParam(':id_usuario', $id_usuario);


if($sentencia->execute()){
        header("Location: " . $URL . "/admin/usuarios?success=actualizado");
        exit;
    }else{
        echo "Error";
        exit;
    }
?>