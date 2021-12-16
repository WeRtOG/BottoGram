<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram;

/**
 * Класс для работы с изображениями
 */
class Images
{
    /**
     * Метод для оптимизации изображения
     * @param string $infile Входной файл
     * @param string $outfile Выходной файл
     * @param float $neww Новая ширина
     * @param float $newh Новая высота
     * @param int $quality Качество
     */
    public static function OptimizeImage(string $infile, string $outfile, float $neww = -1, float $newh = -1, int $quality = 80)
    {
        $ext_out = strtolower(pathinfo($outfile, PATHINFO_EXTENSION));
        $ext_in = strtolower(pathinfo($infile, PATHINFO_EXTENSION));

        $im = match ($ext_in) {
            'jpg', 'jpeg' => imagecreatefromjpeg($infile),
            'png' => imagecreatefrompng($infile),
            'webp' => imagecreatefromwebp($infile),
        };

        /* TODO: need to add validation for image extensions. */

        if ($neww !== -1) {
            $k1 = $neww / imagesx($im);
        } else {
            $k1 = 1;
        }
        if ($newh !== -1) {
            $k2 = $newh / imagesy($im);
        } else {
            $k2 = 1;
        }

        $k = $k1 > $k2 ? $k2 : $k1;

        $w = intval(imagesx($im) * $k);
        $h = intval(imagesy($im) * $k);

        $im1 = imagecreatetruecolor($w, $h);
        imagecopyresampled($im1, $im, 0, 0, 0, 0, $w, $h, imagesx($im), imagesy($im));

        match ($ext_out) {
            'jpg', 'jpeg' => imagejpeg($im1, $outfile, $quality),
            'png' => imagepng($im1, $outfile, -1),
            'webp' => imagepng($im1, $outfile, $quality),
        };

        imagedestroy($im);
        imagedestroy($im1);
    }
}
