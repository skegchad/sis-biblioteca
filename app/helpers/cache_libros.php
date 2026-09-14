<?php

function calcularGrosorCache(int $paginas): float
{
    $MIN_PAG = 80;
    $MAX_PAG = 900;
    $MIN_GROSOR = 0.08;
    $MAX_GROSOR = 0.52;

    $p = max($MIN_PAG, min($MAX_PAG, $paginas));

    $t = ($p - $MIN_PAG) / ($MAX_PAG - $MIN_PAG);

    return $MIN_GROSOR + $t * ($MAX_GROSOR - $MIN_GROSOR);
}


function calcularColorDominante(string $rutaImagenAbsoluta): string
{
    $FALLBACK = '#8C7355';

    if (!is_readable($rutaImagenAbsoluta)) {
        return $FALLBACK;
    }

    $info = @getimagesize($rutaImagenAbsoluta);

    if (!$info) {
        return $FALLBACK;
    }

    switch ($info['mime']) {

        case 'image/jpeg':
            $origen = @imagecreatefromjpeg($rutaImagenAbsoluta);
            break;

        case 'image/png':
            $origen = @imagecreatefrompng($rutaImagenAbsoluta);
            break;

        case 'image/webp':
            $origen = @imagecreatefromwebp($rutaImagenAbsoluta);
            break;

        default:
            return $FALLBACK;
    }

    if (!$origen) {
        return $FALLBACK;
    }

    $muestra = imagecreatetruecolor(32, 32);

    imagealphablending($muestra, false);
    imagesavealpha($muestra, true);

    imagecopyresampled(
        $muestra,
        $origen,
        0,
        0,
        0,
        0,
        32,
        32,
        imagesx($origen),
        imagesy($origen)
    );

    $r = 0;
    $g = 0;
    $b = 0;
    $total = 0;

    for ($x = 0; $x < 32; $x++) {

        for ($y = 0; $y < 32; $y++) {

            $rgb = imagecolorat($muestra, $x, $y);

            $colores = imagecolorsforindex(
                $muestra,
                $rgb
            );

            // Ignorar píxeles demasiado transparentes
            if ($colores['alpha'] > 100) {
                continue;
            }

            $r += $colores['red'];
            $g += $colores['green'];
            $b += $colores['blue'];

            $total++;
        }
    }

    imagedestroy($origen);
    imagedestroy($muestra);

    if ($total === 0) {
        return $FALLBACK;
    }

    return sprintf(
        'rgb(%d,%d,%d)',
        round($r / $total),
        round($g / $total),
        round($b / $total)
    );
}