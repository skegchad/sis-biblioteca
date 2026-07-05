<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
header('Content-Type: application/json');


$categoria_nombre = trim($_GET['categoria'] ?? '');

if (!$categoria_nombre) {
    echo json_encode([]);
    exit;
}

$stmtCat = $pdo->prepare("SELECT id FROM categorias WHERE nombre = ?");
$stmtCat->execute([$categoria_nombre]);
$categoria = $stmtCat->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT nombre FROM subcategorias WHERE categoria_id = ? ORDER BY nombre ASC");
$stmt->execute([$categoria['id']]);

$resultado = [];
foreach ($stmt as $sub) {
    $resultado[] = ['nombre' => $sub['nombre']];
}

echo json_encode($resultado);