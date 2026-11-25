<?php
/** Utilidades para generar páginas JPEG a partir de PDFs con Poppler. */

function pdfPaginasBinario(): string
{
    $configurado = getenv('PDFTOPPM_BIN');
    if ($configurado && is_file($configurado)) return $configurado;
    $instalados = glob('C:/Users/*/AppData/Local/Microsoft/WinGet/Packages/oschwartz10612.Poppler_*/poppler-*/Library/bin/pdftoppm.exe') ?: [];
    if ($instalados) {
        natsort($instalados);
        return end($instalados);
    }
    return 'pdftoppm'; // Debe estar en el PATH del proceso de Apache.
}

function pdfPaginasDirectorio(int $idLibro): string
{
    return dirname(__DIR__, 2) . '/public/uploads/pdf/paginas/' . $idLibro;
}

function pdfPaginasManifest(int $idLibro): ?array
{
    $ruta = pdfPaginasDirectorio($idLibro) . '/manifest.json';
    if (!is_file($ruta)) return null;
    $datos = json_decode((string) file_get_contents($ruta), true);
    return is_array($datos) ? $datos : null;
}

function pdfPaginasCompletas(int $idLibro, string $pdf): bool
{
    $m = pdfPaginasManifest($idLibro);
    return $m && is_file($pdf)
        && (int)($m['source_bytes'] ?? -1) === (int)filesize($pdf)
        && (int)($m['source_mtime'] ?? -1) === (int)filemtime($pdf)
        && (int)($m['pages'] ?? 0) > 0;
}

function pdfPaginasBytesGenerados(int $idLibro): int
{
    $dir = pdfPaginasDirectorio($idLibro);
    $total = 0;
    foreach (glob($dir . '/*.jpg') ?: [] as $archivo) $total += (int)@filesize($archivo);
    return $total;
}

function procesarPdfPaginas(int $idLibro, string $pdf): array
{
    @set_time_limit(0);
    if (!is_file($pdf) || strtolower(pathinfo($pdf, PATHINFO_EXTENSION)) !== 'pdf') {
        throw new RuntimeException('No se encontró un PDF válido.');
    }
    if (pdfPaginasCompletas($idLibro, $pdf)) return ['pages' => (int)pdfPaginasManifest($idLibro)['pages'], 'already' => true];

    $base = pdfPaginasDirectorio($idLibro);
    $tmp = $base . '.tmp-' . bin2hex(random_bytes(4));
    if (!mkdir($tmp, 0775, true) && !is_dir($tmp)) throw new RuntimeException('No se pudo crear la carpeta de páginas.');
    $prefix = $tmp . '/src';
    $cmd = [pdfPaginasBinario(), '-jpeg', '-jpegopt', 'quality=82,optimize=y', '-scale-to', '1800', $pdf, $prefix];
    $spec = [0 => ['pipe','r'], 1 => ['pipe','w'], 2 => ['pipe','w']];
    $proc = @proc_open($cmd, $spec, $pipes, dirname($pdf));
    if (!is_resource($proc)) throw new RuntimeException('Apache no puede ejecutar pdftoppm. Reinicia Apache o configura PDFTOPPM_BIN.');
    fclose($pipes[0]); $stdout = stream_get_contents($pipes[1]); fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]); fclose($pipes[2]); $code = proc_close($proc);
    $files = glob($tmp . '/src-*.jpg') ?: [];
    natsort($files); $files = array_values($files);
    if ($code !== 0 || !$files) {
        foreach (glob($tmp . '/*') ?: [] as $f) @unlink($f);
        @rmdir($tmp);
        throw new RuntimeException('Falló la conversión Poppler: ' . trim($stderr ?: $stdout));
    }
    foreach ($files as $i => $file) {
        $dest = sprintf('%s/page-%04d.jpg', $tmp, $i + 1);
        if (!rename($file, $dest)) throw new RuntimeException('No se pudo ordenar una página generada.');
    }
    $manifest = [
        'version' => 1, 'pages' => count($files), 'format' => 'jpg',
        'source_bytes' => (int)filesize($pdf), 'source_mtime' => (int)filemtime($pdf),
        'generated_at' => date(DATE_ATOM),
    ];
    file_put_contents($tmp . '/manifest.json', json_encode($manifest, JSON_UNESCAPED_SLASHES));
    if (is_dir($base)) {
        foreach (glob($base . '/*') ?: [] as $f) if (is_file($f)) @unlink($f);
        @rmdir($base);
    }
    if (!rename($tmp, $base)) throw new RuntimeException('No se pudo publicar el conjunto de páginas.');
    return ['pages' => count($files), 'already' => false];
}

function borrarPdfOriginalSiProcesado(int $idLibro, string $pdf): int
{
    if (!pdfPaginasCompletas($idLibro, $pdf)) throw new RuntimeException('No se puede borrar: la conversión no está completa.');
    $bytes = (int)filesize($pdf);
    if (!unlink($pdf)) throw new RuntimeException('No se pudo borrar el PDF original.');
    return $bytes;
}
