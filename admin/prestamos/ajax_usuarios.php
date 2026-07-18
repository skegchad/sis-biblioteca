<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../app/config/config.php';
require_once '../../app/config/conexion.php';

if (!isset($_SESSION['sesion_user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$sql = "
    SELECT id_usuario, nombre_completo, cargo, curso, paralelo
    FROM tb_usuarios
    WHERE estado = '1'
      AND fyh_eliminacion IS NULL
    ORDER BY nombre_completo ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($usuarios, JSON_UNESCAPED_UNICODE);