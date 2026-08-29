<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../app/config/config.php';
require_once __DIR__ . '/../../../app/config/conexion.php';

try {

    // Categorías
    $categorias = $pdo->query("
        SELECT id, nombre
        FROM categorias
        ORDER BY nombre ASC
    ")->fetchAll(PDO::FETCH_ASSOC);


    // Subcategorías
    $subcategorias = $pdo->query("
        SELECT id, categoria_id, nombre
        FROM subcategorias
        ORDER BY nombre ASC
    ")->fetchAll(PDO::FETCH_ASSOC);


    // Tipos
    $tipos = $pdo->query("
        SELECT id, nombre
        FROM tipos
        ORDER BY nombre ASC
    ")->fetchAll(PDO::FETCH_ASSOC);


    // Idiomas existentes realmente en los libros
    $idiomas = $pdo->query("
        SELECT DISTINCT idioma
        FROM tb_libros
        WHERE estado = 1
          AND idioma IS NOT NULL
          AND TRIM(idioma) <> ''
        ORDER BY idioma ASC
    ")->fetchAll(PDO::FETCH_COLUMN);


    // Temas
    $temas = $pdo->query("
        SELECT id, tipo_id, nombre
        FROM temas
        ORDER BY nombre ASC
    ")->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode([
        'categorias'    => $categorias,
        'subcategorias' => $subcategorias,
        'tipos'         => $tipos,
        'idiomas'       => $idiomas,
        'temas'         => $temas
    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'error' => true,
        'mensaje' => 'Error al cargar los filtros'
    ], JSON_UNESCAPED_UNICODE);
}