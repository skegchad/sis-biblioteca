<?php
/**
 * Extrae el color dominante (hex) de una imagen de portada usando GD.
 * Reduce la imagen a 40x40, agrupa píxeles similares en "buckets"
 * y descarta blancos/negros casi puros (suelen ser fondo, no portada).
 *
 * @param string $rutaImagen Ruta ABSOLUTA en el servidor a la imagen
 * @return string|null Color en formato "#RRGGBB", o null si falla
 */
function colorDominante(string $rutaImagen): ?string
{
    if (!extension_loaded('gd')) {
        return null;
    }

    if (!file_exists($rutaImagen)) {
        return null;
    }

    $info = @getimagesize($rutaImagen);
    if (!$info) {
        return null;
    }

    switch ($info[2]) {
        case IMAGETYPE_JPEG:
            $img = @imagecreatefromjpeg($rutaImagen);
            break;
        case IMAGETYPE_PNG:
            $img = @imagecreatefrompng($rutaImagen);
            break;
        case IMAGETYPE_WEBP:
            $img = @imagecreatefromwebp($rutaImagen);
            break;
        default:
            return null;
    }
    if (!$img) {
        return null;
    }

    $ancho = imagesx($img);
    $alto = imagesy($img);
    $mini = imagecreatetruecolor(40, 40);
    imagecopyresampled($mini, $img, 0, 0, 0, 0, 40, 40, $ancho, $alto);

    $buckets = [];
    for ($x = 0; $x < 40; $x++) {
        for ($y = 0; $y < 40; $y++) {
            $rgb = imagecolorat($mini, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            // Ignorar casi blanco/negro puro: suele ser fondo o borde, no la portada
            if (($r > 245 && $g > 245 && $b > 245) || ($r < 10 && $g < 10 && $b < 10)) {
                continue;
            }

            // Cuantizar en bloques de 16 para agrupar tonos parecidos
            $key = sprintf('%d-%d-%d', intdiv($r, 16) * 16, intdiv($g, 16) * 16, intdiv($b, 16) * 16);
            if (!isset($buckets[$key])) {
                $buckets[$key] = ['r' => 0, 'g' => 0, 'b' => 0, 'n' => 0];
            }
            $buckets[$key]['r'] += $r;
            $buckets[$key]['g'] += $g;
            $buckets[$key]['b'] += $b;
            $buckets[$key]['n']++;
        }
    }

    imagedestroy($img);
    imagedestroy($mini);

    if (empty($buckets)) {
        return '#8C7355';
    }

    usort($buckets, fn($a, $b) => $b['n'] <=> $a['n']);
    $top = $buckets[0];
    $r = intdiv($top['r'], $top['n']);
    $g = intdiv($top['g'], $top['n']);
    $b = intdiv($top['b'], $top['n']);

    return sprintf('#%02X%02X%02X', $r, $g, $b);
}