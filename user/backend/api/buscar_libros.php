<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../app/config/config.php';
require_once __DIR__ . '/../../../app/config/conexion.php';

require_once __DIR__ . '/../helpers/PdfPages.php';
require_once __DIR__ . '/../helpers/DominantColor.php';

$categoria = trim($_GET['categoria'] ?? '');
$tema      = trim($_GET['tema'] ?? '');
$buscar    = trim($_GET['buscar'] ?? '');

$sql = "SELECT id_libro, titulo, autor, categoria, temas, ruta_pdf, ruta_foto
        FROM tb_libros
        WHERE 1=1";

$params = [];

if ($categoria !== '') {
    $sql .= " AND categoria = :categoria";
    $params[':categoria'] = $categoria;
}

if ($tema !== '') {
    $sql .= " AND temas = :tema";
    $params[':tema'] = $tema;
}

if ($buscar !== '') {
    $sql .= " AND (titulo LIKE :buscar OR autor LIKE :buscar)";
    $params[':buscar'] = "%{$buscar}%";
}

$sql .= " ORDER BY titulo ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

$resultado = [];

foreach ($libros as $libro) {

    $paginas = 150;

    if (!empty($libro['ruta_pdf'])) {
        $rutaAbsolutaPdf =
            $_SERVER['DOCUMENT_ROOT'] . '/' .
            ltrim($libro['ruta_pdf'], '/');

        $paginas = contarPaginasPDF($rutaAbsolutaPdf) ?: $paginas;
    }

    $color = '#8C7355';

    if (!empty($libro['ruta_foto'])) {
        $rutaAbsolutaFoto =
            $_SERVER['DOCUMENT_ROOT'] . '/' .
            ltrim($libro['ruta_foto'], '/');

        $color = colorDominante($rutaAbsolutaFoto) ?: $color;
    }

    $resultado[] = [
        'id'        => (int) $libro['id_libro'],
        'titulo'    => $libro['titulo'],
        'autor'     => $libro['autor'],
        'categoria' => $libro['categoria'],
        'temas'     => $libro['temas'],
        'paginas'   => $paginas,
        'color'     => $color,
        'ruta_foto' => $libro['ruta_foto'],
        'ruta_pdf'  => $libro['ruta_pdf'],
    ];
}

echo json_encode($resultado, JSON_UNESCAPED_UNICODE);