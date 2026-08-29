<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../app/config/config.php';
require_once __DIR__ . '/../../../app/config/conexion.php';

$categorias = $pdo->query("SELECT nombre FROM categorias")
                   ->fetchAll(PDO::FETCH_COLUMN);

$temas = $pdo->query("SELECT nombre FROM temas")
              ->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    'categorias' => $categorias,
    'temas'      => $temas,
], JSON_UNESCAPED_UNICODE);
