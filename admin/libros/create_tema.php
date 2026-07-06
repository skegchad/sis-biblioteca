<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$tipo_nombre = trim($data['tipo'] ?? '');
$nombre = trim($data['nombre'] ?? '');

if (!$tipo_nombre || !$nombre) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$stmtTipo = $pdo->prepare("SELECT id FROM tipos WHERE nombre = ?");
$stmtTipo->execute([$tipo_nombre]);
$tipo = $stmtTipo->fetch(PDO::FETCH_ASSOC);

if (!$tipo) {
    echo json_encode(['error' => 'El tipo no existe']);
    exit;
}

$tipo_id = $tipo['id'];

$check = $pdo->prepare("SELECT nombre FROM temas WHERE tipo_id = ? AND nombre = ?");
$check->execute([$tipo_id, $nombre]);
$existente = $check->fetch(PDO::FETCH_ASSOC);

if ($existente) {
    echo json_encode($existente);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO temas (tipo_id, nombre) VALUES (?, ?)");

if ($stmt->execute([$tipo_id, $nombre])) {
    echo json_encode(['nombre' => $nombre]);
} else {
    echo json_encode(['error' => 'Error al guardar el tema']);
}
