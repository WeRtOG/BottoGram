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

        if($ext_in == 'jpg' || $ext_in == 'jpeg')
        {
            $im = imagecreatefromjpeg($infile);
        }
        if($ext_in == 'png')
        {
            $im = imagecreatefrompng($infile);
        }
        if($ext_in == 'webp')
        {
            $im = imagecreatefromwebp($infile);
        }

        if($neww != -1)
        {
            $k1 = $neww / imagesx($im);
        }
        else
        {
            $k1 = 1;
        }
        if($newh != -1)
        {
            $k2 = $newh / imagesy($im);
        }
        else
        {
            $k2 = 1;
        }

        $k = $k1>$k2?$k2:$k1;

        $w = intval(imagesx($im)*$k);
        $h = intval(imagesy($im)*$k);

        $im1 = imagecreatetruecolor($w,$h);
        imagecopyresampled($im1, $im, 0, 0, 0, 0, $w, $h, imagesx($im), imagesy($im));

        if($ext_out == 'jpg' || $ext_out == 'jpeg')
        {
            imagejpeg($im1, $outfile, $quality);
        }
        if($ext_out == 'png')
        {
            imagepng($im1, $outfile, -1);
        }
        if($ext_out == 'webp')
        {
            imagewebp($im1, $outfile, $quality);
        }

        imagedestroy($im);
        imagedestroy($im1);
    }
}