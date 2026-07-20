<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");

$id_libro = $_GET['id'];
$fyh_eliminacion = $fyh_actual;

$sentencia=$pdo->prepare("UPDATE tb_libros SET
estado = :estado,
fyh_eliminacion = :fyh_eliminacion WHERE id_libro = :id_libro");

$sentencia->bindParam(":estado", $estado_eliminado);
$sentencia->bindParam(":fyh_eliminacion", $fyh_eliminacion);
$sentencia->bindParam(":id_libro", $id_libro);

if($sentencia->execute()){
        header("Location: " . $URL . "/admin/libros?success=eliminado");
        exit;
    }else{
        echo "Error";
        exit;
    }
