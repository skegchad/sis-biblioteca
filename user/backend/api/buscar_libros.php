<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../app/config/config.php';
require_once __DIR__ . '/../../../app/config/conexion.php';

require_once __DIR__ . '/../helpers/PdfPages.php';
require_once __DIR__ . '/../helpers/DominantColor.php';


$RAIZ_PROYECTO = realpath(__DIR__ . '/../../../');

if (isset($_GET['resumen'])) {
    $resumen = $pdo->query("SELECT categoria, COUNT(*) AS total
        FROM tb_libros
        WHERE estado = 1
        GROUP BY categoria
        ORDER BY categoria ASC")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($resumen, JSON_UNESCAPED_UNICODE);
    exit;
}


// ============================================================
// FILTROS
// ============================================================

$categoria = trim($_GET['categoria'] ?? '');
$subcategoria = trim($_GET['subcategoria'] ?? '');
$tipo = trim($_GET['tipo'] ?? '');
$idioma = trim($_GET['idioma'] ?? '');
$disponibilidad = trim($_GET['disponibilidad'] ?? '');
$buscar = trim($_GET['buscar'] ?? '');


$temas = $_GET['temas'] ?? [];

if (!is_array($temas)) {
    $temas = [$temas];
}

$temas = array_values(
    array_filter(
        array_map('intval', $temas),
        fn($id) => $id > 0
    )
);


// ============================================================
// CONSULTA BASE
// ============================================================

$sql = "SELECT
    l.id_libro,
    l.titulo,
    l.autor,
    l.descripcion,
    l.idioma,
    l.disponibilidad,
    l.temas,
    l.tipo,
    l.edicion,
    l.ano,
    l.cdd,
    l.bloque,
    l.categoria,
    l.subcategoria,
    l.seccion,
    l.editorial,
    l.ejemplares,
    l.prestados,
    l.ruta_pdf,
    l.ruta_foto,
    l.paginas_pdf_cache,
    l.paginas_pdf_cache_key,
    l.color_portada_cache,
    l.color_portada_cache_key
FROM tb_libros AS l
WHERE l.estado = 1";

$params = [];


// ============================================================
// CATEGORÍA
// ============================================================

if ($categoria !== '') {

    $sql .= " AND l.categoria = :categoria";

    $params[':categoria'] = $categoria;
}


// ============================================================
// SUBCATEGORÍA
// ============================================================

if ($subcategoria !== '') {

    $sql .= " AND l.subcategoria = :subcategoria";

    $params[':subcategoria'] = $subcategoria;
}


// ============================================================
// TIPO
// ============================================================

if ($tipo !== '') {

    $sql .= " AND l.tipo = :tipo";

    $params[':tipo'] = $tipo;
}


// ============================================================
// IDIOMA
// ============================================================

if ($idioma !== '') {

    $sql .= " AND l.idioma = :idioma";

    $params[':idioma'] = $idioma;
}


// ============================================================
// BÚSQUEDA
// ============================================================

if ($buscar !== '') {

    $sql .= "
        AND (
            l.titulo LIKE :buscar
            OR l.autor LIKE :buscar
        )
    ";

    $params[':buscar'] = "%{$buscar}%";
}


// ============================================================
// DISPONIBILIDAD
// ============================================================

if ($disponibilidad === 'disponible') {

    $sql .= "
        AND l.ejemplares > l.prestados
    ";

}

elseif ($disponibilidad === 'no_disponible') {

    $sql .= "
        AND l.ejemplares <= l.prestados
    ";
}


// ============================================================
// TEMAS (tabla de relación muchos-a-muchos: libro_tema)
// ============================================================

if (!empty($temas)) {

    // Genera :tema0, :tema1, :tema2... para el IN (...)
    $placeholders = [];

    foreach ($temas as $i => $temaId) {
        $placeholder = ":tema{$i}";
        $placeholders[] = $placeholder;
        $params[$placeholder] = $temaId;
    }

    $listaPlaceholders = implode(', ', $placeholders);

    $sql .= "
        AND l.id_libro IN (
            SELECT lt.id_libro
            FROM libro_tema AS lt
            WHERE lt.tema_id IN ({$listaPlaceholders})
        )
    ";
}


// ============================================================
// PAGINACIÓN opcional. Sin `limite` se conserva la respuesta completa.
// El catálogo usa páginas pequeñas para no transferir todo el catálogo.
$limite = isset($_GET['limite']) ? max(1, min(48, (int) $_GET['limite'])) : null;
$offset = max(0, (int) ($_GET['offset'] ?? 0));
$total = null;

if ($limite !== null) {
    $posicionFrom = strpos($sql, 'FROM tb_libros');
    $sqlConteo = 'SELECT COUNT(*) ' . substr($sql, $posicionFrom);
    $stmtConteo = $pdo->prepare($sqlConteo);
    $stmtConteo->execute($params);
    $total = (int) $stmtConteo->fetchColumn();
}

// ORDEN
// ============================================================

$sql .= " ORDER BY l.titulo ASC";

if ($limite !== null) {
    $sql .= ' LIMIT :limite OFFSET :offset';
}


// ============================================================
// EJECUTAR
// ============================================================

$stmt = $pdo->prepare($sql);
foreach ($params as $nombre => $valor) {
    $stmt->bindValue($nombre, $valor);
}
if ($limite !== null) {
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    header('X-Total-Count: ' . $total);
}
$stmt->execute();

$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ============================================================
// CONSTRUIR RESULTADO
// ============================================================

$resultado = [];

$debug = isset($_GET['debug']);

$actualizarCache = $pdo->prepare("UPDATE tb_libros
    SET paginas_pdf_cache = :paginas,
        paginas_pdf_cache_key = :pdf_key,
        color_portada_cache = :color,
        color_portada_cache_key = :foto_key
    WHERE id_libro = :id");


foreach ($libros as $libro) {

    $debugInfo = [];


    // --------------------------------------------------------
    // PÁGINAS PDF
    // --------------------------------------------------------

    $paginas = 150;
    $pdfKey = null;

    if (!empty($libro['ruta_pdf'])) {

        $rutaAbsolutaPdf =
            $RAIZ_PROYECTO . '/' .
            ltrim($libro['ruta_pdf'], '/');

        $pdfStat = @stat($rutaAbsolutaPdf);
        if ($pdfStat) {
            $pdfKey = hash('sha256', $libro['ruta_pdf'] . '|' . $pdfStat['mtime'] . '|' . $pdfStat['size']);
            if ($libro['paginas_pdf_cache_key'] === $pdfKey && $libro['paginas_pdf_cache'] !== null) {
                $paginas = (int) $libro['paginas_pdf_cache'];
                $paginasCalculadas = $paginas;
            } else {
                $paginasCalculadas = contarPaginasPDF($rutaAbsolutaPdf);
                $paginas = $paginasCalculadas ?: $paginas;
            }
        } else {
            $paginasCalculadas = null;
        }


        if ($debug) {

            $debugInfo['ruta_pdf_absoluta'] =
                $rutaAbsolutaPdf;

            $debugInfo['archivo_existe'] =
                file_exists($rutaAbsolutaPdf);

            $debugInfo['paginas_calculadas'] =
                $paginasCalculadas;
        }
    }


    // --------------------------------------------------------
    // COLOR PORTADA
    // --------------------------------------------------------

    $color = '#8C7355';
    $fotoKey = null;

    if (!empty($libro['ruta_foto'])) {

        $rutaAbsolutaFoto =
            $RAIZ_PROYECTO . '/' .
            ltrim($libro['ruta_foto'], '/');

        $fotoStat = @stat($rutaAbsolutaFoto);
        if ($fotoStat) {
            $fotoKey = hash('sha256', $libro['ruta_foto'] . '|' . $fotoStat['mtime'] . '|' . $fotoStat['size']);
            if ($libro['color_portada_cache_key'] === $fotoKey && !empty($libro['color_portada_cache'])) {
                $colorCalculado = $libro['color_portada_cache'];
            } else {
                $colorCalculado = colorDominante($rutaAbsolutaFoto);
            }
            $color = $colorCalculado ?: $color;
        } else {
            $colorCalculado = null;
        }


        if ($debug) {

            $debugInfo['ruta_foto_absoluta'] =
                $rutaAbsolutaFoto;

            $debugInfo['foto_existe'] =
                file_exists($rutaAbsolutaFoto);

            $debugInfo['color_calculado'] =
                $colorCalculado;
        }
    }


    // Guardar los cálculos solo cuando cambian los archivos fuente.
    if (($pdfKey !== null && ($libro['paginas_pdf_cache_key'] !== $pdfKey || $libro['paginas_pdf_cache'] === null)) ||
        ($fotoKey !== null && ($libro['color_portada_cache_key'] !== $fotoKey || empty($libro['color_portada_cache'])))) {
        $actualizarCache->execute([
            ':paginas' => $paginas,
            ':pdf_key' => $pdfKey,
            ':color' => $color,
            ':foto_key' => $fotoKey,
            ':id' => (int) $libro['id_libro'],
        ]);
    }

    // --------------------------------------------------------
    // DISPONIBILIDAD REAL
    // --------------------------------------------------------

    $ejemplares = (int) $libro['ejemplares'];
    $prestados = (int) $libro['prestados'];

    $estaDisponible =
        $ejemplares > $prestados;



    // --------------------------------------------------------
    // RESULTADO
    // --------------------------------------------------------

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
            'ano'              => $libro['ano'],
            'cdd'              => $libro['cdd'],
            'bloque'           => $libro['bloque'],
            'categoria'        => $libro['categoria'],
            'subcategoria'     => $libro['subcategoria'],
            'seccion'          => $libro['seccion'],
            'editorial'        => $libro['editorial'],
            'ejemplares'       => (int) $libro['ejemplares'],
            'prestados'        => (int) $libro['prestados'],
            'paginas'          => $paginas,
            'color'            => $color,
            'ruta_foto'        => $libro['ruta_foto'],
            'ruta_pdf'         => $libro['ruta_pdf'],
        ];


    if ($debug) {
        $item['debug'] = $debugInfo;
    }


    $resultado[] = $item;
}


// ============================================================
// RESPUESTA
// ============================================================

echo json_encode(
    $resultado,
    JSON_UNESCAPED_UNICODE
);
