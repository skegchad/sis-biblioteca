<?php

include("../../app/config/config.php");
include("../../app/config/conexion.php");
include("../../layout/admin/login.php");
include("../../layout/admin/datos_usuario.php");
include("../../layout/admin/comprueba_admin.php");

require '../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// ===== Traer los datos con JOIN para mostrar nombre de libro y usuario en vez de IDs =====
$query = $pdo->prepare("
    SELECT
        p.id_prestamo,
        l.titulo AS libro,
        CONCAT(u.nombre_completo, ' ', u.apellidos) AS usuario,
        p.fyh_creacion,
        p.fyh_devolucion
    FROM prestamos p
    INNER JOIN tb_libros l ON l.id_libro = p.id_libro
    INNER JOIN tb_usuarios u ON u.id_usuario = p.id_usuario
    ORDER BY p.id_prestamo ASC
");
$query->execute();
$prestamos = $query->fetchAll(PDO::FETCH_ASSOC);

if (count($prestamos) === 0) {
    die("No hay préstamos registrados para exportar.");
}

$columnas = array_keys($prestamos[0]);

// Nombres personalizados para las columnas en el encabezado del PDF
$etiquetas = [
    'id_prestamo'    => 'ID',
    'libro'          => 'Libro',
    'usuario'        => 'Usuario',
    'fyh_creacion'   => 'Fecha préstamo',
    'fyh_devolucion' => 'Fecha devolución',
];

function etiquetaColumna(string $col, array $etiquetas): string
{
    return $etiquetas[strtolower($col)] ?? ucfirst(str_replace('_', ' ', $col));
}

// ===== Armar el HTML de la tabla =====
$html = '
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 5px; }
        p.fecha { text-align: center; color: #666; margin-top: 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; word-wrap: break-word; }
        th { background-color: #0d6efd; color: #fff; }
        tr:nth-child(even) { background-color: #f4f6f8; }
    </style>
</head>
<body>
    <h2>Reporte de Préstamos</h2>
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

foreach ($prestamos as $prestamo) {
    $html .= '<tr>';
    foreach ($columnas as $col) {
        $valor = $prestamo[$col] ?? '';

        if (strtolower($col) === 'fyh_devolucion' && ($valor === null || $valor === '')) {
            $valor = 'Sin devolver';
        }

        $html .= '<td>' . htmlspecialchars($valor) . '</td>';
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
$nombreArchivo = "reporte_prestamos.pdf";
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