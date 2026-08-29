<?php
/**
 * DIAGNÓSTICO TEMPORAL — bórralo cuando termines de usarlo.
 * Colócalo en la MISMA carpeta que buscar_libros.php y ábrelo
 * directo en el navegador (sin parámetros).
 */
require_once __DIR__ . '/../../../app/config/config.php';
require_once __DIR__ . '/../../../app/config/conexion.php';
require_once __DIR__ . '/../helpers/PdfPages.php';
require_once __DIR__ . '/../helpers/DominantColor.php';

header('Content-Type: text/plain; charset=utf-8');

echo "DOCUMENT_ROOT reportado por PHP: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Este script está en: " . __DIR__ . "\n";
echo str_repeat('=', 70) . "\n\n";

$stmt = $pdo->query("SELECT id_libro, titulo, ruta_pdf, ruta_foto
                      FROM tb_libros
                      WHERE (ruta_pdf IS NOT NULL AND ruta_pdf <> '')
                         OR (ruta_foto IS NOT NULL AND ruta_foto <> '')
                      LIMIT 3");
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$libros) {
    echo "No encontré ningún libro con ruta_pdf o ruta_foto llenos. Revisa esos campos en la BD.\n";
    exit;
}

foreach ($libros as $libro) {
    echo "### {$libro['titulo']} (id {$libro['id_libro']})\n\n";

    if (!empty($libro['ruta_pdf'])) {
        $rutaPdf = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($libro['ruta_pdf'], '/');
        echo "PDF -> ruta guardada en BD:      {$libro['ruta_pdf']}\n";
        echo "PDF -> ruta absoluta calculada:  $rutaPdf\n";
        $existePdf = file_exists($rutaPdf);
        echo "PDF -> ¿existe el archivo ahí?:  " . ($existePdf ? 'SI' : 'NO') . "\n";
        if ($existePdf) {
            echo "PDF -> tamaño:                   " . filesize($rutaPdf) . " bytes\n";
            $paginas = contarPaginasPDF($rutaPdf);
            echo "PDF -> paginas detectadas:       " . var_export($paginas, true) . "\n";
        }
    } else {
        echo "PDF -> ruta_pdf está vacío en la BD para este libro.\n";
    }

    echo "\n";

    if (!empty($libro['ruta_foto'])) {
        $rutaFoto = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($libro['ruta_foto'], '/');
        echo "FOTO -> ruta guardada en BD:     {$libro['ruta_foto']}\n";
        echo "FOTO -> ruta absoluta calculada: $rutaFoto\n";
        $existeFoto = file_exists($rutaFoto);
        echo "FOTO -> ¿existe el archivo ahí?: " . ($existeFoto ? 'SI' : 'NO') . "\n";
        if ($existeFoto) {
            $color = colorDominante($rutaFoto);
            echo "FOTO -> color detectado:         " . var_export($color, true) . "\n";
        }
    } else {
        echo "FOTO -> ruta_foto está vacío en la BD para este libro.\n";
    }

    echo "\n" . str_repeat('-', 70) . "\n\n";
}