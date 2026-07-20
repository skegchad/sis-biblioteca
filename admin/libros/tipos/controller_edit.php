<?php
include ("../../../app/config/config.php");
include ("../../../app/config/conexion.php");
include ("../../../layout/admin/login.php");
include ("../../../layout/admin/comprueba_admin.php");

// ===== 1. Validar datos básicos =====
$id     = $_POST['id'] ?? null;
$nombre = trim($_POST['Nombre'] ?? '');

if (!$id) {
    header("Location: " . $URL . "/admin/libros/tipos?error=id_invalido");
    exit;
}

if (empty($nombre)) {
    header("Location: " . $URL . "/admin/libros/tipos/edit.php?id=" . $id . "&error=nombre_vacio");
    exit;
}

// ===== 2. Confirmar que el tipo existe =====
$queryActual = $pdo->prepare("SELECT id FROM tipos WHERE id = :id");
$queryActual->execute([':id' => $id]);
$tipoActual = $queryActual->fetch(PDO::FETCH_ASSOC);

if (!$tipoActual) {
    header("Location: " . $URL . "/admin/libros/tipos?error=no_encontrado");
    exit;
}

// ===== 3. Actualizar el tipo y sus temas =====
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE tipos SET nombre = :nombre WHERE id = :id");
    $stmt->execute([
        ':nombre' => $nombre,
        ':id'     => $id
    ]);

    // ---- Comparar temas actuales vs. los que llegaron del formulario ----

    // Temas que ya existen en la BD para este tipo
    $queryActuales = $pdo->prepare("SELECT id, nombre FROM temas WHERE tipo_id = :id");
    $queryActuales->execute([':id' => $id]);
    $actuales = $queryActuales->fetchAll(PDO::FETCH_ASSOC);
    $nombresActuales = array_column($actuales, 'nombre');

    // Temas que llegaron desde el formulario
    $temasRaw = trim($_POST['temas'] ?? '');
    $nuevos = !empty($temasRaw)
        ? array_filter(array_map('trim', explode(',', $temasRaw)))
        : [];

    // Los que hay que insertar: están en $nuevos pero no en $nombresActuales
    $aInsertar = array_diff($nuevos, $nombresActuales);

    if (!empty($aInsertar)) {
        $stmtInsert = $pdo->prepare("INSERT INTO temas (tipo_id, nombre, fyh_creacion) VALUES (:tipo_id, :nombre, NOW())");
        foreach ($aInsertar as $nombreTema) {
            $stmtInsert->execute([
                ':tipo_id' => $id,
                ':nombre'  => $nombreTema
            ]);
        }
    }

    // Los que hay que borrar: estaban en $nombresActuales pero ya no vienen en $nuevos
    $aBorrar = array_diff($nombresActuales, $nuevos);

    if (!empty($aBorrar)) {
        $placeholders = implode(',', array_fill(0, count($aBorrar), '?'));
        $stmtBorrar = $pdo->prepare("DELETE FROM temas WHERE tipo_id = ? AND nombre IN ($placeholders)");
        $stmtBorrar->execute(array_merge([$id], array_values($aBorrar)));
    }

    // Los temas que quedaron igual (ni en $aInsertar ni en $aBorrar) mantienen su id original

    $pdo->commit();

    header("Location: " . $URL . "/admin/libros/tipos?success=actualizado");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: " . $URL . "/admin/libros/tipos/edit.php?id=" . $id . "&error=db");
    exit;
}