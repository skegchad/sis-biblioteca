<?php
require_once (__DIR__ . "/../../app/config/config.php");
require_once (__DIR__ . "/../../app/config/conexion.php");
header('Content-Type: application/json');

$tipo_nombre = trim($_GET['tipo'] ?? '');

if (!$tipo_nombre) {
    echo json_encode([]);
    exit;
}

$stmtTipo = $pdo->prepare("SELECT id FROM tipos WHERE nombre = ?");
$stmtTipo->execute([$tipo_nombre]);
$tipo = $stmtTipo->fetch(PDO::FETCH_ASSOC);

if (!$tipo) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT nombre FROM temas WHERE tipo_id = ? ORDER BY nombre ASC");
$stmt->execute([$tipo['id']]);

$resultado = [];
foreach ($stmt as $tema) {
    $resultado[] = ['nombre' => $tema['nombre']];
}

echo json_encode($resultado);