<?php

include("../../app/config/config.php");
include("../../app/config/conexion.php");
include("../../layout/admin/login.php");
include("../../layout/admin/datos_usuario.php");

// $id y $rutaFoto ya vienen resueltos por datos_usuario.php

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== 0) {
    header("Location: ".$URL."/user/profile/index.php?error=imagen");
    exit;
}

$archivo    = $_FILES['foto'];
$extension  = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
$permitidos = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($extension, $permitidos)) {
    header("Location: ".$URL."/user/profile/index.php?error=formato");
    exit;
}

if ($archivo['size'] > 5 * 1024 * 1024) { // 5 MB
    header("Location: ".$URL."/user/profile/index.php?error=tamano");
    exit;
}

if (@getimagesize($archivo['tmp_name']) === false) {
    header("Location: ".$URL."/user/profile/index.php?error=formato");
    exit;
}

try {

    $carpeta          = $ROOT."public/uploads/img/admin/";
    $nombreArchivo    = "usuario".$id.".".$extension;
    $destino_absoluto = $carpeta.$nombreArchivo;
    $destino_bd        = "public/uploads/img/admin/".$nombreArchivo;

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    if (!move_uploaded_file($archivo['tmp_name'], $destino_absoluto)) {
        throw new Exception("No se pudo mover la imagen.");
    }

    // Solo tocamos la BD si la ruta guardada actualmente NO coincide con
    // el archivo que acabamos de generar (ej: usuario tenía .jpg y ahora
    // subió un .png, o todavía tenía la foto default). En ese caso:
    // 1) actualizamos ruta_foto con el nuevo nombre/extensión
    // 2) borramos el archivo anterior, si existe y es distinto al nuevo
    if ($rutaFoto !== $destino_bd) {

        $rutaAnteriorAbsoluta = $ROOT . ltrim($rutaFoto, '/');

        // Solo borramos si no es la imagen default (esa la queremos conservar)
        // y si efectivamente existe en disco.
        $esDefault = (basename($rutaFoto) === 'default.jpg');

        if (!$esDefault && file_exists($rutaAnteriorAbsoluta)) {
            unlink($rutaAnteriorAbsoluta);
        }

        $update = $pdo->prepare("UPDATE tb_usuarios SET foto = :foto WHERE id_usuario = :id");
        $update->execute([
            ':foto' => $destino_bd,
            ':id'   => $id
        ]);
    }

    header("Location: ".$URL."/user/profile/index.php?success=foto");
    exit;

} catch (Exception $e) {
    header("Location: ".$URL."/user/profile/index.php?error=db");
    exit;
}