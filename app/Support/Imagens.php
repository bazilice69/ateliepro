<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Salva imagens REDIMENSIONADAS e COMPRIMIDAS, para economizar disco, deixar a
 * galeria/catálogo rápidos e evitar erro de upload com fotos grandes de celular.
 *
 * Se a extensão GD não estiver disponível no servidor, salva o arquivo original
 * (sem quebrar o upload) — degradação graciosa.
 */
class Imagens
{
    /**
     * @return string caminho salvo (relativo ao disk public)
     */
    public static function salvarComprimida(UploadedFile $arquivo, string $pasta, int $maxLado = 1600, int $qualidade = 80): string
    {
        $destino = $pasta.'/'.Str::random(40).'.jpg';

        // Sem GD? Guarda o original (preserva a extensão).
        if (! function_exists('imagecreatetruecolor')) {
            return $arquivo->store($pasta, 'public');
        }

        $origem = $arquivo->getRealPath();
        $info = @getimagesize($origem);
        if ($info === false) {
            return $arquivo->store($pasta, 'public');
        }

        [$largura, $altura] = $info;
        $tipo = $info[2];

        $img = match ($tipo) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($origem),
            IMAGETYPE_PNG  => @imagecreatefrompng($origem),
            IMAGETYPE_GIF  => @imagecreatefromgif($origem),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($origem) : null,
            default        => null,
        };

        if (! $img) {
            return $arquivo->store($pasta, 'public');
        }

        // Corrige orientação de fotos de celular (EXIF).
        if ($tipo === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($origem);
            if (! empty($exif['Orientation'])) {
                $img = match ((int) $exif['Orientation']) {
                    3 => imagerotate($img, 180, 0),
                    6 => imagerotate($img, -90, 0),
                    8 => imagerotate($img, 90, 0),
                    default => $img,
                };
                $largura = imagesx($img);
                $altura = imagesy($img);
            }
        }

        // Redimensiona mantendo a proporção (lado maior <= $maxLado).
        $escala = min(1, $maxLado / max($largura, $altura));
        $novaL = max(1, (int) round($largura * $escala));
        $novaA = max(1, (int) round($altura * $escala));

        $redim = imagecreatetruecolor($novaL, $novaA);
        $branco = imagecolorallocate($redim, 255, 255, 255);
        imagefilledrectangle($redim, 0, 0, $novaL, $novaA, $branco);
        imagecopyresampled($redim, $img, 0, 0, 0, 0, $novaL, $novaA, $largura, $altura);

        Storage::disk('public')->makeDirectory($pasta);
        imagejpeg($redim, Storage::disk('public')->path($destino), $qualidade);

        imagedestroy($img);
        imagedestroy($redim);

        return $destino;
    }
}
