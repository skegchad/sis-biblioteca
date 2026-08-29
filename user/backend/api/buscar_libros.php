<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../app/config/config.php';
require_once __DIR__ . '/../../../app/config/conexion.php';

require_once __DIR__ . '/../helpers/PdfPages.php';
require_once __DIR__ . '/../helpers/DominantColor.php';

/**
 * DOCUMENT_ROOT en tu XAMPP apunta a C:/xampp/htdocs (la raíz de todo
 * XAMPP), no a la carpeta del proyecto — por eso las rutas no se
 * encontraban. Aquí calculamos la raíz REAL del proyecto a partir de
 * dónde está este archivo: user/backend/api/ -> subimos 3 niveles.
 */
$RAIZ_PROYECTO = realpath(__DIR__ . '/../../../');

$categoria = trim($_GET['categoria'] ?? '');
$tema      = trim($_GET['tema'] ?? '');
$buscar    = trim($_GET['buscar'] ?? '');

$sql = "SELECT
            id_libro,
            titulo,
            autor,
            descripcion,
            idioma,
            disponibilidad,
            temas,
            tipo,
            edicion,
            ano,
            cdd,
            bloque,
            categoria,
            subcategoria,
            seccion,
            editorial,
            ejemplares,
            prestados,
            ruta_pdf,
            ruta_foto
        FROM tb_libros
        WHERE estado=1";

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

$debug = isset($_GET['debug']);
$resultado = [];

foreach ($libros as $libro) {
    $debugInfo = [];

    $paginas = 150;

    if (!empty($libro['ruta_pdf'])) {
        $rutaAbsolutaPdf = $RAIZ_PROYECTO . '/' . ltrim($libro['ruta_pdf'], '/');
        $paginasCalculadas = contarPaginasPDF($rutaAbsolutaPdf);
        $paginas = $paginasCalculadas ?: $paginas;

        if ($debug) {
            $debugInfo['ruta_pdf_absoluta'] = $rutaAbsolutaPdf;
            $debugInfo['archivo_existe'] = file_exists($rutaAbsolutaPdf);
            $debugInfo['paginas_calculadas'] = $paginasCalculadas;
        }
    }

    $color = '#8C7355';

    if (!empty($libro['ruta_foto'])) {
        $rutaAbsolutaFoto = $RAIZ_PROYECTO . '/' . ltrim($libro['ruta_foto'], '/');
        $colorCalculado = colorDominante($rutaAbsolutaFoto);
        $color = $colorCalculado ?: $color;

        if ($debug) {
            $debugInfo['ruta_foto_absoluta'] = $rutaAbsolutaFoto;
            $debugInfo['foto_existe'] = file_exists($rutaAbsolutaFoto);
            $debugInfo['color_calculado'] = $colorCalculado;
        }
    }

    $item = [
        'id'              => (int) $libro['id_libro'],
        'titulo'          => $libro['titulo'],
        'autor'           => $libro['autor'],
        'descripcion'     => $libro['descripcion'],
        'idioma'          => $libro['idioma'],
        'disponibilidad'  => (int) $libro['disponibilidad'],
        'temas'           => $libro['temas'],
        'tipo'            => $libro['tipo'],
        'edicion'         => $libro['edicion'],
        'ano'             => $libro['ano'],
        'cdd'             => $libro['cdd'],
        'bloque'          => $libro['bloque'],
        'categoria'       => $libro['categoria'],
        'subcategoria'    => $libro['subcategoria'],
        'seccion'         => $libro['seccion'],
        'editorial'       => $libro['editorial'],
        'ejemplares'      => (int) $libro['ejemplares'],
        'prestados'       => (int) $libro['prestados'],
        'paginas'         => $paginas,
        'color'           => $color,
        'ruta_foto'       => $libro['ruta_foto'],
        'ruta_pdf'        => $libro['ruta_pdf'],
    ];
    if ($debug) {
        $item['debug'] = $debugInfo;
    }
    $resultado[] = $item;
}

echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
