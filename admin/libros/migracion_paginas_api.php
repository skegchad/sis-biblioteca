<?php
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../layout/admin/login.php';
require_once __DIR__ . '/../../layout/admin/datos_usuario.php';
require_once __DIR__ . '/../../layout/admin/comprueba_admin.php';
require_once __DIR__ . '/../../app/helpers/pdf_paginas.php';
header('Content-Type: application/json; charset=utf-8');

function respuestaJson(array $data, int $status = 200): never {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    $accion = $_POST['accion'] ?? $_GET['accion'] ?? 'estado';
    $libros = $pdo->query("SELECT id_libro, titulo, ruta_pdf FROM tb_libros WHERE ruta_pdf IS NOT NULL AND ruta_pdf <> '' ORDER BY id_libro")->fetchAll(PDO::FETCH_ASSOC);
    $raiz = dirname(__DIR__, 2);
    $pendientes = 0; $procesados = 0; $bytesLiberables = 0; $bytesPaginas = 0;
    foreach ($libros as $libro) {
        $pdf = $raiz . '/' . ltrim($libro['ruta_pdf'], '/\\');
        if (!is_file($pdf)) continue;
        if (pdfPaginasCompletas((int)$libro['id_libro'], $pdf)) {
            $procesados++;
            $bytesLiberables += (int)filesize($pdf);
            $bytesPaginas += pdfPaginasBytesGenerados((int)$libro['id_libro']);
        } else $pendientes++;
    }
    if ($accion === 'estado') respuestaJson(['total' => count($libros), 'pendientes' => $pendientes, 'procesados' => $procesados, 'bytes_liberables' => $bytesLiberables, 'bytes_paginas' => $bytesPaginas, 'ahorro_neto_estimado' => $bytesLiberables - $bytesPaginas]);
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') respuestaJson(['error' => 'Método no permitido'], 405);

    if ($accion === 'procesar') {
        foreach ($libros as $libro) {
            $pdf = $raiz . '/' . ltrim($libro['ruta_pdf'], '/\\');
            if (!is_file($pdf) || pdfPaginasCompletas((int)$libro['id_libro'], $pdf)) continue;
            $r = procesarPdfPaginas((int)$libro['id_libro'], $pdf);
            respuestaJson(['ok' => true, 'id' => (int)$libro['id_libro'], 'titulo' => $libro['titulo'], 'pages' => $r['pages'], 'pendientes' => max(0, $pendientes - 1)]);
        }
        respuestaJson(['fin' => true, 'pendientes' => 0]);
    }
    if ($accion === 'borrar') {
        foreach ($libros as $libro) {
            $pdf = $raiz . '/' . ltrim($libro['ruta_pdf'], '/\\');
            if (!is_file($pdf) || !pdfPaginasCompletas((int)$libro['id_libro'], $pdf)) continue;
            $bytes = borrarPdfOriginalSiProcesado((int)$libro['id_libro'], $pdf);
            $pdo->prepare('UPDATE tb_libros SET ruta_pdf = NULL WHERE id_libro = ?')->execute([(int)$libro['id_libro']]);
            respuestaJson(['ok' => true, 'id' => (int)$libro['id_libro'], 'titulo' => $libro['titulo'], 'bytes_liberados' => $bytes]);
        }
        respuestaJson(['fin' => true, 'bytes_liberados' => 0]);
    }
    respuestaJson(['error' => 'Acción desconocida'], 400);
} catch (Throwable $e) {
    error_log('Migración de páginas: ' . $e->getMessage());
    respuestaJson(['error' => $e->getMessage()], 500);
}
