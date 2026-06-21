<?php


$servidor = "mysql:dbname=".BD_SISTEMA.";host=".BD_SERVIDOR;

try{
    $pdo = new PDO($servidor, BD_USUARIO, BD_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
}catch (PDOException $e){
    die("<script>alert('Error al conectar a la base de datos.');</script>");
}
