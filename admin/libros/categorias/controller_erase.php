<?php
include ("../../../app/config/config.php");
include ("../../../app/config/conexion.php");
include ("../../../layout/admin/login.php");
include ("../../../layout/admin/comprueba_admin.php");

$id_categoria = $_GET['id'] ?? null;

if (!$id_categoria) {
    header("Location: " . $URL . "/admin/libros/categorias?error=id_invalido");
    exit;
}

try {
    $pdo->beginTransaction();

    // ===== 1. Obtener la foto para borrarla del servidor (si no es el default) =====
    $queryFoto = $pdo->prepare("SELECT foto FROM categorias WHERE id = :id");
    $queryFoto->execute([':id' => $id_categoria]);
    $categoria = $queryFoto->fetch(PDO::FETCH_ASSOC);

    if (!$categoria) {
        $pdo->rollBack();
        header("Location: " . $URL . "/admin/libros/categorias?error=no_encontrada");
        exit;
    }

    $foto = trim($categoria['foto']);

    // ===== 2. Borrar las subcategorías vinculadas =====
    $stmtSub = $pdo->prepare("DELETE FROM subcategorias WHERE categoria_id = :id");
    $stmtSub->execute([':id' => $id_categoria]);

    // ===== 3. Borrar la categoría =====
    $stmtCat = $pdo->prepare("DELETE FROM categorias WHERE id = :id");
    $stmtCat->execute([':id' => $id_categoria]);

    $pdo->commit();

    // ===== 4. Borrar el archivo de foto del servidor (fuera de la transacción) =====
    if ($foto !== 'public/uploads/img/libros/categorias/default.jpg') {
        $rutaFoto = $ROOT . $foto;
        if (file_exists($rutaFoto)) {
            unlink($rutaFoto);
        }
    }

    header("Location: " . $URL . "/admin/libros/categorias?success=eliminado");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: " . $URL . "/admin/libros/categorias?error=db");
    exit;
}