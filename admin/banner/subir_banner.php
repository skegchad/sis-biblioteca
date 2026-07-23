<?php
include("../../app/config/config.php");
include("../../app/config/conexion.php");
include("../../layout/admin/login.php");
include("../../layout/admin/datos_usuario.php");
include("../../layout/admin/comprueba_admin.php");

header('Content-Type: application/json');

if ($cargo !== 'Administrador') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if (!isset($_FILES['banner']) || $_FILES['banner']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No se recibió ninguna imagen']);
    exit;
}

// Whitelist de archivos permitidos (para evitar que sobrescriban cualquier ruta)
$archivosPermitidos = ['libross.jpeg', 'libross2.jpeg'];
$nombreArchivo = $_POST['archivo'] ?? '';

if (!in_array($nombreArchivo, $archivosPermitidos)) {
    echo json_encode(['success' => false, 'message' => 'Archivo destino no válido']);
    exit;
}

$archivo = $_FILES['banner'];

$tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
$tipoDetectado = mime_content_type($archivo['tmp_name']);

if (!in_array($tipoDetectado, $tiposPermitidos)) {
    echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido']);
    exit;
}

$destino = $ROOT . 'public/assets/img/grupoProyecto/' . $nombreArchivo;

if (move_uploaded_file($archivo['tmp_name'], $destino)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo guardar la imagen']);
}