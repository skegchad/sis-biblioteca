<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");

$id_usuario = $_GET['id'];
$fyh_eliminacion = $fyh_actual;

$sentencia=$pdo->prepare("UPDATE tb_usuarios SET
estado = :estado,
fyh_eliminacion = :fyh_eliminacion WHERE id_usuario = :id_usuario");

$sentencia->bindParam(":estado", $estado_eliminado);
$sentencia->bindParam(":fyh_eliminacion", $fyh_eliminacion);
$sentencia->bindParam(":id_usuario", $id_usuario);

if($sentencia->execute()){
        header("Location: " . $URL . "/admin/usuarios?success=eliminado");
        exit;
    }else{
        echo "Error";
        exit;
    }
