<?php

include("../app/config/config.php");
include("../app/config/conexion.php");
include("../layout/admin/login.php");
include("../layout/admin/comprueba_admin.php");

// ===== Validar imagen =====
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== 0) {
    header("Location: ".$URL."/user?error=imagen");
    exit;
}

$archivo    = $_FILES['imagen'];
$extension  = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
$permitidos = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($extension, $permitidos)) {
    header("Location: ".$URL."/user?error=formato");
    exit;
}

if ($archivo['size'] > 5 * 1024 * 1024) { // 5 MB
    header("Location: ".$URL."/user?error=tamano");
    exit;
}

// Verifica que el archivo sea realmente una imagen válida
if (@getimagesize($archivo['tmp_name']) === false) {
    header("Location: ".$URL."/user?error=formato");
    exit;
}

$id_noticia = null;
$destino_absoluto = null;

try {

    $pdo->beginTransaction();

    // 1. Insertamos primero con ruta vacía → obtenemos un id único garantizado
    $query = $pdo->prepare("INSERT INTO noticias (ruta_foto) VALUES ('')");
    $query->execute();
    $id_noticia = $pdo->lastInsertId();

    // 2. Ahora sí, nombramos el archivo con el id real
    $nombreArchivo     = "noticia".$id_noticia.".".$extension;
    $destino_absoluto  = $ROOT."public/uploads/img/noticias/".$nombreArchivo;
    $destino_bd        = "public/uploads/img/noticias/".$nombreArchivo;

    if (!move_uploaded_file($archivo['tmp_name'], $destino_absoluto)) {
        throw new Exception("No se pudo mover la imagen.");
    }

    // 3. Actualizamos la fila con la ruta final
    $update = $pdo->prepare("UPDATE noticias SET ruta_foto = :ruta_foto WHERE id_noticia = :id_noticia");
    $update->execute([
        ':ruta_foto'  => $destino_bd,
        ':id_noticia' => $id_noticia
    ]);

    $pdo->commit();

    header("Location: ".$URL."/user?success=noticia");
    exit;

} catch (Exception $e) {

    $pdo->rollBack();

    // Si el archivo llegó a moverse antes de fallar el update, lo borramos
    // para no dejar huérfanos en el servidor
    if ($destino_absoluto && file_exists($destino_absoluto)) {
        unlink($destino_absoluto);
    }

    header("Location: ".$URL."/user?error=db");
    exit;
}