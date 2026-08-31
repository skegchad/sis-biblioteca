<?php
include ("../../../app/config/config.php");
include ("../../../app/config/conexion.php");
include ("../../../layout/admin/login.php");
include ("../../../layout/admin/datos_usuario.php");
include ("../../../layout/admin/comprueba_admin.php");
// ===== 1. Validar y recoger datos del formulario =====
$nombre = trim($_POST['Nombre'] ?? '');

if (empty($nombre)) {
    header("Location: " . $URL . "/admin/libros/tipos/create.php?error=nombre_vacio");
    exit;
}

// ===== 2. Insertar el tipo =====
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO tipos (nombre, fyh_creacion) VALUES (:nombre, NOW())");
    $stmt->execute([
        ':nombre' => $nombre
    ]);

    $tipoId = $pdo->lastInsertId();

    // ===== 3. Insertar los temas (si el usuario agregó alguno) =====
    $temasRaw = trim($_POST['temas'] ?? '');

    if (!empty($temasRaw)) {
        $listaTemas = explode(',', $temasRaw);
        $listaTemas = array_filter(array_map('trim', $listaTemas));

        $stmtTemas = $pdo->prepare("INSERT INTO temas (tipo_id, nombre, fyh_creacion) VALUES (:tipo_id, :nombre, NOW())");

        foreach ($listaTemas as $tema) {
            $stmtTemas->execute([
                ':tipo_id' => $tipoId,
                ':nombre'  => $tema
            ]);
        }
    }

    $pdo->commit();

    header("Location: " . $URL . "/admin/libros/tipos?success=registrado");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: " . $URL . "/admin/libros/tipos/create.php?error=db");
    exit;
}