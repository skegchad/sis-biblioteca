<?php

include("../../app/config/config.php");
include("../../app/config/conexion.php");
include("../../layout/admin/login.php");
include("../../layout/admin/datos_usuario.php");

header('Content-Type: application/json');

// $id ya viene resuelto por datos_usuario.php

$nuevoUsuario = trim($_POST['nombre_usuario'] ?? '');

if (strlen($nuevoUsuario) < 3) {
    echo json_encode(['success' => false, 'message' => 'El usuario debe tener al menos 3 caracteres.']);
    exit;
}

// Si el usuario "nuevo" es igual al que ya tenía, no hace falta hacer nada
if ($nuevoUsuario === $nombreusuario) {
    echo json_encode(['success' => true]);
    exit;
}

try {

    // Verificar que el nombre de usuario no esté en uso por otra persona
    $check = $pdo->prepare("SELECT id_usuario FROM tb_usuarios WHERE nombre_usuario = :usuario AND id_usuario != :id");
    $check->execute([
        ':usuario' => $nuevoUsuario,
        ':id'      => $id
    ]);

    if ($check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ese nombre de usuario ya está en uso.']);
        exit;
    }

    $update = $pdo->prepare("UPDATE tb_usuarios SET nombre_usuario = :usuario, fyh_actualizacion = NOW() WHERE id_usuario = :id");
    $update->execute([
        ':usuario' => $nuevoUsuario,
        ':id'      => $id
    ]);

    // Muy importante: la sesión guarda el nombre_usuario (no el id).
    // Si no la actualizamos acá, datos_usuario.php buscaría en el próximo
    // request por el nombre viejo, que ya no existe en la BD.
    $_SESSION['sesion_user'] = $nuevoUsuario;

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar en la base de datos.']);
}