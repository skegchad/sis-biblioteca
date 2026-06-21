<?php
include ("../app/config/config.php");
include ("../app/config/conexion.php");

$user = $_POST['user'];
$password = $_POST['password'];

$query_login = $pdo->prepare("SELECT * FROM tb_usuarios WHERE nombre_usuario = '$user' AND estado = '1' ");
$query_login->execute();

$usuarios = $query_login->fetchAll(PDO::FETCH_ASSOC);

$contador = 0;

foreach ($usuarios as $usuario){
    $contador++;
    $nombres = $usuario['nombre_completo'];
    $nombreusuario = $usuario['nombre_usuario'];
    $passwordBD = $usuario['password'];
}

if($contador>0){
    if (password_verify($password, $passwordBD)) {
    
    session_start();
    $_SESSION['sesion_user']=$user;
    echo "<script>
        alert('Bienvenido $nombreusuario.');
        window.location.href = '".$URL."/admin/';
    </script>";
    
} else {
    header("Location: ".$URL."/login/index.php?error=contrasena");
}
}else{
    header("Location: ".$URL."/login/index.php?error=usuario");
}


?>