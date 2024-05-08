<?php
session_start();
header('Content-type: image/png');

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$permitted_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
$length = 4;  $captcha_text = substr(str_shuffle($permitted_chars), 0, $length);

$_SESSION['captcha'] = $captcha_text;

$image = imagecreatetruecolor(120, 40);
$background_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
imagefilledrectangle($image, 0, 0, 120, 40, $background_color);

$line_color = imagecolorallocate($image, 64, 64, 64);
for ($i = 0; $i < 6; $i++) {
    imageline($image, mt_rand(0, 120), mt_rand(0, 40), mt_rand(0, 120), mt_rand(0, 40), $line_color);
}

for ($i = 0; $i < 1000; $i++) {
    imagesetpixel($image, mt_rand(0, 120), mt_rand(0, 40), $text_color);
}

$font = '../ttf/CascadiaMono.ttf'; imagettftext($image, 20, 0, 30, 30, $text_color, $font, $captcha_text);

imagepng($image);
imagedestroy($image);
