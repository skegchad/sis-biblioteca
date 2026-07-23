<?php

include("../app/config/config.php");
include("../app/config/conexion.php");
include("../layout/admin/login.php");
include("../layout/admin/comprueba_admin.php");

// ===== Validar el id =====
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: ".$URL."/user?error=id_invalido");
    exit;
}

$id_noticia = (int) $_GET['id'];

try {

    $pdo->beginTransaction();

    // Buscamos la noticia para obtener la ruta del archivo antes de borrar la fila
    $query = $pdo->prepare("SELECT ruta_foto FROM noticias WHERE id_noticia = :id");
    $query->execute([':id' => $id_noticia]);
    $noticia = $query->fetch(PDO::FETCH_ASSOC);

    if (!$noticia) {
        $pdo->rollBack();
        header("Location: ".$URL."/user?error=no_existe");
        exit;
    }

    // Borramos la fila de la BD
    $delete = $pdo->prepare("DELETE FROM noticias WHERE id_noticia = :id");
    $delete->execute([':id' => $id_noticia]);

    $pdo->commit();

    // Solo si la BD se actualizó bien, borramos el archivo físico
    $rutaArchivo = $ROOT . ltrim($noticia['ruta_foto'], '/');
    if (file_exists($rutaArchivo)) {
        unlink($rutaArchivo);
    }

    header("Location: ".$URL."/user?success=eliminada");
    exit;

} catch (Exception $e) {

    $pdo->rollBack();
    header("Location: ".$URL."/user?error=db");
    exit;
}