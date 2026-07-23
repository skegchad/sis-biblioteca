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
$query = $pdo->prepare("SELECT * FROM tb_usuarios ORDER BY id_usuario ASC");
$query->execute();
$usuarios = $query->fetchAll(PDO::FETCH_ASSOC);

if (count($usuarios) === 0) {
    die("No hay usuarios registrados para exportar.");
}

// Columnas a excluir del PDF (contraseñas, hashes, tokens, foto, etc.)
$columnasExcluidas = ['password', 'contrasena', 'clave', 'token', 'foto', 'estado'];

$columnas = array_keys($usuarios[0]);
$columnas = array_filter($columnas, function ($col) use ($columnasExcluidas) {
    return !in_array(strtolower($col), $columnasExcluidas);
});

// Nombres personalizados para las columnas en el encabezado del PDF
$etiquetas = [
    'id_usuario'      => 'ID',
    'nombre_completo' => 'Nombre',
    'nombre_usuario'  => 'Usuario',
];

// Columnas donde un valor NULL/vacío significa "no aplica" (no es estudiante)
$columnasNoEstudiante = ['curso', 'paralelo'];

function etiquetaColumna(string $col, array $etiquetas): string
{
    return $etiquetas[strtolower($col)] ?? ucfirst($col);
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
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background-color: #0d6efd; color: #fff; }
        tr:nth-child(even) { background-color: #f4f6f8; }
    </style>
</head>
<body>
    <h2>Reporte de Usuarios</h2>
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

foreach ($usuarios as $usuario) {
    $html .= '<tr>';
    foreach ($columnas as $col) {
        $valor = $usuario[$col] ?? '';

        if (in_array(strtolower($col), $columnasNoEstudiante) && ($valor === null || $valor === '')) {
            $valor = 'No es est.';
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
$nombreArchivo = "reporte_usuarios.pdf";
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