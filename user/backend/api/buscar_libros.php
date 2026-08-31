<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../app/config/config.php';
require_once __DIR__ . '/../../../app/config/conexion.php';

require_once __DIR__ . '/../helpers/PdfPages.php';
require_once __DIR__ . '/../helpers/DominantColor.php';


$RAIZ_PROYECTO = realpath(__DIR__ . '/../../../');


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
    l.ruta_foto
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
// ORDEN
// ============================================================

$sql .= " ORDER BY l.titulo ASC";


// ============================================================
// EJECUTAR
// ============================================================

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ============================================================
// CONSTRUIR RESULTADO
// ============================================================

$resultado = [];

$debug = isset($_GET['debug']);


foreach ($libros as $libro) {

    $debugInfo = [];


    // --------------------------------------------------------
    // PÁGINAS PDF
    // --------------------------------------------------------

    $paginas = 150;

    if (!empty($libro['ruta_pdf'])) {

        $rutaAbsolutaPdf =
            $RAIZ_PROYECTO . '/' .
            ltrim($libro['ruta_pdf'], '/');

        $paginasCalculadas =
            contarPaginasPDF($rutaAbsolutaPdf);

        $paginas =
            $paginasCalculadas ?: $paginas;


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

    if (!empty($libro['ruta_foto'])) {

        $rutaAbsolutaFoto =
            $RAIZ_PROYECTO . '/' .
            ltrim($libro['ruta_foto'], '/');

        $colorCalculado =
            colorDominante($rutaAbsolutaFoto);

        $color =
            $colorCalculado ?: $color;


        if ($debug) {

            $debugInfo['ruta_foto_absoluta'] =
                $rutaAbsolutaFoto;

            $debugInfo['foto_existe'] =
                file_exists($rutaAbsolutaFoto);

            $debugInfo['color_calculado'] =
                $colorCalculado;
        }
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