<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");

$id_usuario      = $_GET['id'];
$nombre_completo = $_POST['Nombres'];
$apellidos       = $_POST['Apellidos'];
$cedula          = $_POST['Cedula'];
$cargo           = $_POST['Cargo'];
$nombre_usuario  = $_POST['Nombreusuario'];
$curso           = !empty($_POST['Curso'])    ? $_POST['Curso']    : null;
$paralelo        = !empty($_POST['Paralelo']) ? $_POST['Paralelo'] : null;
$fyh_actualizacion = $fyh_actual;

// ── Foto ──────────────────────────────────────────────────────────────
// Traer la foto actual para no pisarla si no se sube una nueva
$stFoto = $pdo->prepare("SELECT foto FROM tb_usuarios WHERE id_usuario = :id");
$stFoto->bindParam(':id', $id_usuario);
$stFoto->execute();
$rutaFoto = $stFoto->fetchColumn(); // conserva la foto actual por defecto

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $archivo    = $_FILES['foto'];
    $extension  = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $permitidos)) {
        header("Location: " . $URL . "/admin/usuarios/edit.php?id=" . $id_usuario . "&error=formato");
        exit;
    }

    if ($archivo['size'] > 2 * 1024 * 1024) {
        header("Location: " . $URL . "/admin/usuarios/edit.php?id=" . $id_usuario . "&error=tamano");
        exit;
    }

    $nombreArchivo    = uniqid('user_') . '.' . $extension;
    $destino_absoluto = $ROOT . "public/uploads/img/admin/" . $nombreArchivo;
    $destino_bd       = "public/uploads/img/admin/" . $nombreArchivo;

    if (move_uploaded_file($archivo['tmp_name'], $destino_absoluto)) {
        $rutaFoto = $destino_bd;
    } else {
        header("Location: " . $URL . "/admin/usuarios/edit.php?id=" . $id_usuario . "&error=subida");
        exit;
    }
}
// ──────────────────────────────────────────────────────────────────────

$sentencia = $pdo->prepare("UPDATE tb_usuarios SET
    nombre_completo    = :nombre_completo,
    apellidos          = :apellidos,
    cedula             = :cedula,
    cargo              = :cargo,
    nombre_usuario     = :nombre_usuario,
    curso              = :curso,
    paralelo           = :paralelo,
    foto               = :foto,
    fyh_actualizacion  = :fyh_actualizacion
    WHERE id_usuario   = :id_usuario");

$sentencia->bindParam(':nombre_completo',   $nombre_completo);
$sentencia->bindParam(':apellidos',         $apellidos);
$sentencia->bindParam(':cedula',            $cedula);
$sentencia->bindParam(':cargo',             $cargo);
$sentencia->bindParam(':nombre_usuario',    $nombre_usuario);
$sentencia->bindParam(':curso',             $curso);
$sentencia->bindParam(':paralelo',          $paralelo);
$sentencia->bindParam(':foto',              $rutaFoto);
$sentencia->bindParam(':fyh_actualizacion', $fyh_actualizacion);
$sentencia->bindParam(':id_usuario',        $id_usuario);

if ($sentencia->execute()) {
    header("Location: " . $URL . "/admin/usuarios?success=actualizado");
    exit;
} else {
    header("Location: " . $URL . "/admin/usuarios/edit.php?id=" . $id_usuario . "&error=bd");
    exit;
}