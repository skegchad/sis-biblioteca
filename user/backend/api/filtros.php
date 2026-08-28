<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../app/config/config.php';
require_once __DIR__ . '/../../../app/config/conexion.php';

$categorias = $pdo->query("SELECT DISTINCT categoria FROM tb_libros WHERE categoria IS NOT NULL AND categoria <> '' ORDER BY categoria")
                   ->fetchAll(PDO::FETCH_COLUMN);

$temas = $pdo->query("SELECT DISTINCT temas FROM tb_libros WHERE temas IS NOT NULL AND temas <> '' ORDER BY temas")
              ->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    'categorias' => $categorias,
    'temas'      => $temas,
], JSON_UNESCAPED_UNICODE);
