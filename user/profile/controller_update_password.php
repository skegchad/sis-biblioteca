<?php

include("../../app/config/config.php");
include("../../app/config/conexion.php");
include("../../layout/admin/login.php");
include("../../layout/admin/datos_usuario.php");

header('Content-Type: application/json');

// $id ya viene resuelto por datos_usuario.php

$actual = $_POST['actual'] ?? '';
$nueva  = $_POST['nueva'] ?? '';

if (strlen($nueva) < 6) {
    echo json_encode(['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres.']);
    exit;
}

try {

    $query = $pdo->prepare("SELECT password FROM tb_usuarios WHERE id_usuario = :id");
    $query->execute([':id' => $id]);
    $usuarioActual = $query->fetch(PDO::FETCH_ASSOC);

    if (!$usuarioActual) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
        exit;
    }

    // Verificar que la contraseña actual sea correcta
    if (!password_verify($actual, $usuarioActual['password'])) {
        echo json_encode(['success' => false, 'message' => 'La contraseña actual es incorrecta.']);
        exit;
    }

    $nuevoHash = password_hash($nueva, PASSWORD_BCRYPT);

    $update = $pdo->prepare("UPDATE tb_usuarios SET password = :password, fyh_actualizacion = NOW() WHERE id_usuario = :id");
    $update->execute([
        ':password' => $nuevoHash,
        ':id'       => $id
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar en la base de datos.']);
}