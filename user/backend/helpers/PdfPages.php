<?php
/**
 * Cuenta las páginas de un PDF leyendo su estructura interna,
 * sin depender de librerías externas (no siempre disponibles
 * en hosting compartido).
 *
 * @param string $rutaPdf Ruta ABSOLUTA en el servidor al archivo .pdf
 * @return int|null Número de páginas, o null si no se pudo determinar
 */
function contarPaginasPDF(string $rutaPdf): ?int
{
    if (!file_exists($rutaPdf)) {
        return null;
    }

    $contenido = @file_get_contents($rutaPdf);
    if ($contenido === false) {
        return null;
    }

    // Método 1 (más confiable): el objeto raíz /Type /Pages trae /Count N, en texto plano
    if (preg_match('/\/Type\s*\/Pages[^>]*\/Count\s+(\d+)/s', $contenido, $m)) {
        return (int) $m[1];
    }

    // Método 2: contar objetos /Type /Page en texto plano (cuidando de no
    // contar /Type /Pages, por eso el [^s] después de "Page")
    $conteo = preg_match_all('/\/Type\s*\/Page[^s]/', $contenido);

    // Método 3: muchos PDFs modernos guardan sus objetos comprimidos dentro
    // de streams (FlateDecode / object streams), así que el texto de arriba
    // no aparece "plano" en el archivo. Buscamos cada stream, lo
    // descomprimimos, y repetimos la búsqueda ahí adentro.
    if (preg_match_all('/stream\r?\n(.*?)endstream/s', $contenido, $streams)) {
        foreach ($streams[1] as $streamData) {
            $streamData = rtrim($streamData, "\r\n");

            $descomprimido = @gzuncompress($streamData);
            if ($descomprimido === false) {
                // Algunos streams no traen el header zlib completo de 2 bytes
                $descomprimido = @gzinflate(substr($streamData, 2));
            }
            if ($descomprimido === false) {
                continue;
            }

            if (preg_match('/\/Type\s*\/Pages[^>]*\/Count\s+(\d+)/s', $descomprimido, $m)) {
                return (int) $m[1];
            }
            $conteo += preg_match_all('/\/Type\s*\/Page[^s]/', $descomprimido);
        }
    }

    return $conteo > 0 ? $conteo : null;
}
