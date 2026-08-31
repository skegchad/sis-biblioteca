<?php
include ("../../../app/config/config.php");
include ("../../../app/config/conexion.php");
include ("../../../layout/admin/login.php");
include ("../../../layout/admin/datos_usuario.php");
include ("../../../layout/admin/comprueba_admin.php");
// ===== 1. Validar datos básicos =====
$id     = $_POST['id'] ?? null;
$nombre = trim($_POST['Nombre'] ?? '');

if (!$id) {
    header("Location: " . $URL . "/admin/libros/categorias?error=id_invalido");
    exit;
}

if (empty($nombre)) {
    header("Location: " . $URL . "/admin/libros/categorias/edit.php?id=" . $id . "&error=nombre_vacio");
    exit;
}

// ===== 2. Confirmar que la categoría existe =====
$queryActual = $pdo->prepare("SELECT foto FROM categorias WHERE id = :id");
$queryActual->execute([':id' => $id]);
$categoriaActual = $queryActual->fetch(PDO::FETCH_ASSOC);

if (!$categoriaActual) {
    header("Location: " . $URL . "/admin/libros/categorias?error=no_encontrada");
    exit;
}

// Por defecto, se mantiene la foto que ya tenía
$rutaFoto = trim($categoriaActual['foto']);

// ===== 3. Manejar la foto (solo si el usuario subió una nueva) =====
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $archivo    = $_FILES['foto'];
    $extension  = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $permitidos)) {
        header("Location: " . $URL . "/admin/libros/categorias/edit.php?id=" . $id . "&error=formato");
        exit;
    }

    if ($archivo['size'] > 2 * 1024 * 1024) {
        header("Location: " . $URL . "/admin/libros/categorias/edit.php?id=" . $id . "&error=tamano");
        exit;
    }

    $nombreArchivo    = uniqid('cat_') . '.' . $extension;
    $destino_absoluto = $ROOT . "public/uploads/img/libros/categorias/" . $nombreArchivo;
    $destino_bd       = "public/uploads/img/libros/categorias/" . $nombreArchivo;

    if (move_uploaded_file($archivo['tmp_name'], $destino_absoluto)) {

        // Borramos la foto anterior del servidor, siempre que no sea el default
        $fotoAnteriorRuta = $ROOT . $rutaFoto;
        if ($rutaFoto !== 'public/uploads/img/libros/categorias/default.jpg' && file_exists($fotoAnteriorRuta)) {
            unlink($fotoAnteriorRuta);
        }

        $rutaFoto = $destino_bd;
    } else {
        header("Location: " . $URL . "/admin/libros/categorias/edit.php?id=" . $id . "&error=subida");
        exit;
    }
}

// ===== 4. Actualizar la categoría y sus subcategorías =====
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE categorias SET nombre = :nombre, foto = :foto WHERE id = :id");
    $stmt->execute([
        ':nombre' => $nombre,
        ':foto'   => $rutaFoto,
        ':id'     => $id
    ]);

    // ---- Comparar subcategorías actuales vs. las que llegaron del formulario ----

    // Subcategorías que ya existen en la BD para esta categoría
    $queryActuales = $pdo->prepare("SELECT id, nombre FROM subcategorias WHERE categoria_id = :id");
    $queryActuales->execute([':id' => $id]);
    $actuales = $queryActuales->fetchAll(PDO::FETCH_ASSOC);
    $nombresActuales = array_column($actuales, 'nombre');

    // Subcategorías que llegaron desde el formulario
    $subcategoriasRaw = trim($_POST['subcategorias'] ?? '');
    $nuevas = !empty($subcategoriasRaw)
        ? array_filter(array_map('trim', explode(',', $subcategoriasRaw)))
        : [];

    // Las que hay que insertar: están en $nuevas pero no en $nombresActuales
    $aInsertar = array_diff($nuevas, $nombresActuales);

    if (!empty($aInsertar)) {
        $stmtInsert = $pdo->prepare("INSERT INTO subcategorias (categoria_id, nombre, fyh_creacion) VALUES (:categoria_id, :nombre, NOW())");
        foreach ($aInsertar as $nombreSub) {
            $stmtInsert->execute([
                ':categoria_id' => $id,
                ':nombre'       => $nombreSub
            ]);
        }
    }

    // Las que hay que borrar: estaban en $nombresActuales pero ya no vienen en $nuevas
    $aBorrar = array_diff($nombresActuales, $nuevas);

    if (!empty($aBorrar)) {
        $placeholders = implode(',', array_fill(0, count($aBorrar), '?'));
        $stmtBorrar = $pdo->prepare("DELETE FROM subcategorias WHERE categoria_id = ? AND nombre IN ($placeholders)");
        $stmtBorrar->execute(array_merge([$id], array_values($aBorrar)));
    }

    // Las subcategorías que quedaron igual (ni en $aInsertar ni en $aBorrar) mantienen su id original

    $pdo->commit();

    header("Location: " . $URL . "/admin/libros/categorias?success=actualizado");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: " . $URL . "/admin/libros/categorias/edit.php?id=" . $id . "&error=db");
    exit;
}