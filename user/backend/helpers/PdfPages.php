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

    // Método 1 (más confiable): el objeto raíz /Type /Pages trae /Count N
    if (preg_match('/\/Type\s*\/Pages[^>]*\/Count\s+(\d+)/s', $contenido, $m)) {
        return (int) $m[1];
    }

    // Método 2 (fallback): contar objetos /Type /Page (cuidando de no
    // contar /Type /Pages, por eso el [^s] después de "Page")
    $conteo = preg_match_all('/\/Type\s*\/Page[^s]/', $contenido);
    if ($conteo > 0) {
        return $conteo;
    }

    return null;
}
