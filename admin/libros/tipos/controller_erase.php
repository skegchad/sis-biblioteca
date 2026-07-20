<?php
include ("../../../app/config/config.php");
include ("../../../app/config/conexion.php");
include ("../../../layout/admin/login.php");
include ("../../../layout/admin/comprueba_admin.php");

$id_tipo = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id_tipo) {
    header("Location: " . $URL . "/admin/libros/tipos?error=id_invalido");
    exit;
}

try {
    $pdo->beginTransaction();

    // ===== 1. Confirmar que el tipo existe =====
    $queryTipo = $pdo->prepare("SELECT id FROM tipos WHERE id = :id");
    $queryTipo->execute([':id' => $id_tipo]);
    $tipo = $queryTipo->fetch(PDO::FETCH_ASSOC);

    if (!$tipo) {
        $pdo->rollBack();
        header("Location: " . $URL . "/admin/libros/tipos?error=no_encontrado");
        exit;
    }

    // ===== 2. Borrar los temas vinculados =====
    $stmtTemas = $pdo->prepare("DELETE FROM temas WHERE tipo_id = :id");
    $stmtTemas->execute([':id' => $id_tipo]);

    // ===== 3. Borrar el tipo =====
    $stmtTipo = $pdo->prepare("DELETE FROM tipos WHERE id = :id");
    $stmtTipo->execute([':id' => $id_tipo]);

    $pdo->commit();

    header("Location: " . $URL . "/admin/libros/tipos?success=eliminado");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: " . $URL . "/admin/libros/tipos?error=db");
    exit;
}