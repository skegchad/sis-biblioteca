<?php
include ("../../../app/config/config.php");
include ("../../../app/config/conexion.php");
include ("../../../layout/admin/login.php");
include ("../../../layout/admin/comprueba_admin.php");

// ===== 1. Validar y recoger datos del formulario =====
$nombre = trim($_POST['Nombre'] ?? '');

if (empty($nombre)) {
    header("Location: " . $URL . "/admin/libros/categorias/create.php?error=nombre_vacio");
    exit;
}

// ✅ Valor por defecto
$rutaFoto = "public/uploads/img/libros/categorias/default.jpg";

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $archivo    = $_FILES['foto'];
    $extension  = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $permitidos)) {
        header("Location: " . $URL . "/admin/categorias/create.php?error=formato");
        exit;
    }

    if ($archivo['size'] > 2 * 1024 * 1024) {
        header("Location: " . $URL . "/admin/categorias/create.php?error=tamano");
        exit;
    }

    $nombreArchivo    = uniqid('cat_') . '.' . $extension;
    $destino_absoluto = $ROOT . "public/uploads/img/libros/categorias/" . $nombreArchivo;
    $destino_bd       = "public/uploads/img/libros/categorias/" . $nombreArchivo;

    if (move_uploaded_file($archivo['tmp_name'], $destino_absoluto)) {
        $rutaFoto = $destino_bd;
    } else {
        // No se pudo mover, queda el default
        header("Location: " . $URL . "/admin/libros/categorias/create.php?error=subida");
        exit;
    }
}

// ===== 3. Insertar la categoría =====
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO categorias (nombre, foto, fyh_creacion) VALUES (:nombre, :foto, NOW())");
    $stmt->execute([
        ':nombre' => $nombre,
        ':foto'   => $rutaFoto
    ]);

    $categoriaId = $pdo->lastInsertId();

    // ===== 4. Insertar las subcategorías (si el usuario agregó alguna) =====
    $subcategoriasRaw = trim($_POST['subcategorias'] ?? '');

    if (!empty($subcategoriasRaw)) {
        $listaSubcategorias = explode(',', $subcategoriasRaw);
        $listaSubcategorias = array_filter(array_map('trim', $listaSubcategorias));

        $stmtSub = $pdo->prepare("INSERT INTO subcategorias (categoria_id, nombre, fyh_creacion) VALUES (:categoria_id, :nombre, NOW())");

        foreach ($listaSubcategorias as $sub) {
            $stmtSub->execute([
                ':categoria_id' => $categoriaId,
                ':nombre'       => $sub
            ]);
        }
    }

    $pdo->commit();

    header("Location: " . $URL . "/admin/libros/categorias?success=registrado");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: " . $URL . "/admin/libros/categorias/create.php?error=db");
    exit;
}