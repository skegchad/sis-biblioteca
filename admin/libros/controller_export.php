<?php

include("../../app/config/config.php");
include("../../app/config/conexion.php");
include("../../layout/admin/login.php");
include("../../layout/admin/datos_usuario.php");
include("../../layout/admin/comprueba_admin.php");

require '../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// ===== Traer los datos =====
$query = $pdo->prepare("SELECT * FROM tb_libros ORDER BY id_libro ASC");
$query->execute();
$libros = $query->fetchAll(PDO::FETCH_ASSOC);

if (count($libros) === 0) {
    die("No hay libros registrados para exportar.");
}

// Columnas a excluir del PDF
$columnasExcluidas = ['ruta_pdf', 'ruta_foto', 'estado'];

$columnas = array_keys($libros[0]);
$columnas = array_filter($columnas, function ($col) use ($columnasExcluidas) {
    return !in_array(strtolower($col), $columnasExcluidas);
});

// Nombres personalizados para las columnas en el encabezado del PDF
$etiquetas = [
    'id_libro' => 'ID',
    'cdd'      => 'CDD',
];

// Longitud máxima de caracteres antes de truncar (para columnas largas)
$limitesTexto = [
    'descripcion' => 80,
];

function etiquetaColumna(string $col, array $etiquetas): string
{
    return $etiquetas[strtolower($col)] ?? ucfirst(str_replace('_', ' ', $col));
}

function truncarTexto($valor, string $col, array $limites): string
{
    $limite = $limites[strtolower($col)] ?? null;
    if ($limite === null || mb_strlen((string) $valor) <= $limite) {
        return (string) $valor;
    }
    return mb_substr((string) $valor, 0, $limite) . '...';
}

// ===== Armar el HTML de la tabla =====
$html = '
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        h2 { text-align: center; margin-bottom: 5px; }
        p.fecha { text-align: center; color: #666; margin-top: 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; }
        th, td { border: 1px solid #ccc; padding: 5px 6px; text-align: left; word-wrap: break-word; overflow-wrap: break-word; }
        th { background-color: #0d6efd; color: #fff; font-size: 10px; }
        tr:nth-child(even) { background-color: #f4f6f8; }
        td.descripcion { font-size: 9px; }
    </style>
</head>
<body>
    <h2>Reporte de Libros</h2>
    <p class="fecha">Generado el ' . date('d/m/Y H:i') . '</p>
    <table>
        <thead>
            <tr>';

foreach ($columnas as $col) {
    $html .= '<th>' . htmlspecialchars(etiquetaColumna($col, $etiquetas)) . '</th>';
}

$html .= '
            </tr>
        </thead>
        <tbody>';

foreach ($libros as $libro) {
    $html .= '<tr>';
    foreach ($columnas as $col) {
        $valor = $libro[$col] ?? '';
        $valor = truncarTexto($valor, $col, $limitesTexto);
        $clase = strtolower($col) === 'descripcion' ? ' class="descripcion"' : '';
        $html .= '<td' . $clase . '>' . htmlspecialchars($valor) . '</td>';
    }
    $html .= '</tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

// ===== Generar el PDF =====
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// ===== Guardarlo en admin/pdf =====
$nombreArchivo = "reporte_libros.pdf";
$rutaCarpeta   = $ROOT . "admin/pdf/";
$rutaAbsoluta  = $rutaCarpeta . $nombreArchivo;
$rutaPublica   = $URL . "/admin/pdf/" . $nombreArchivo;

if (!is_dir($rutaCarpeta)) {
    if (!mkdir($rutaCarpeta, 0755, true) && !is_dir($rutaCarpeta)) {
        die("No se pudo crear la carpeta de destino: " . $rutaCarpeta);
    }
}

file_put_contents($rutaAbsoluta, $dompdf->output());

// ===== Abrir en el navegador =====
header("Location: " . $rutaPublica . "?t=" . time());
exit;