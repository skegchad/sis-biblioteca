<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");

$nombre_completo = $_POST['Nombres'];
$apellidos       = $_POST['Apellidos'];
$cedula          = $_POST['Cedula'];
$cargo           = $_POST['Cargo'];
$nombre_usuario  = $_POST['Nombreusuario'];
$curso           = !empty($_POST['Curso'])    ? $_POST['Curso']    : null;
$paralelo        = !empty($_POST['Paralelo']) ? $_POST['Paralelo'] : null;
$password        = $_POST['Password'];
$verifyPassword  = $_POST['verifyPassword'];
$passwordbd      = password_hash($password, PASSWORD_DEFAULT);

// ✅ Valor por defecto
$rutaFoto = "public/uploads/img/admin/default.jpg";

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $archivo    = $_FILES['foto'];
    $extension  = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $permitidos)) {
        header("Location: " . $URL . "/admin/usuarios/create.php?error=formato");
        exit;
    }

    if ($archivo['size'] > 2 * 1024 * 1024) {
        header("Location: " . $URL . "/admin/usuarios/create.php?error=tamano");
        exit;
    }

    $nombreArchivo    = uniqid('user_') . '.' . $extension;
    $destino_absoluto = $ROOT . "public/uploads/img/admin/" . $nombreArchivo;
    $destino_bd       = "public/uploads/img/admin/" . $nombreArchivo;

    if (move_uploaded_file($archivo['tmp_name'], $destino_absoluto)) {
        $rutaFoto = $destino_bd;
    } else {
        // No se pudo mover, queda el default
        header("Location: " . $URL . "/admin/usuarios/create.php?error=subida");
        exit;
    }
}

if ($password != $verifyPassword) {
    header("Location: " . $URL . "/admin/usuarios/create.php?error=contrasena");
    exit;
}

$sentencia = $pdo->prepare("INSERT INTO tb_usuarios 
    (nombre_completo, apellidos, cedula, nombre_usuario, password, foto, cargo, fyh_creacion, curso, paralelo, estado)
    VALUES 
    (:nombre_completo, :apellidos, :cedula, :nombre_usuario, :password, :foto, :cargo, :fyh_creacion, :curso, :paralelo, :estado)");

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
$sentencia->bindParam(':foto',            $rutaFoto);

if ($sentencia->execute()) {
    header("Location: " . $URL . "/admin/usuarios?success=registrado");
    exit;
} else {
    header("Location: " . $URL . "/admin/usuarios/create.php?error=bd");
    exit;
}