<?php
session_start();
header('Content-type: image/png');

// 防止浏览器缓存图片
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// 生成新的随机字符串
$permitted_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
$length = 4;  // 维持长度为4
$captcha_text = substr(str_shuffle($permitted_chars), 0, $length);

// 将验证码文本存储到SESSION中以供验证
$_SESSION['captcha'] = $captcha_text;

// 创建验证码图片
$image = imagecreatetruecolor(120, 40);
$background_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
imagefilledrectangle($image, 0, 0, 120, 40, $background_color);

// 添加干扰线
$line_color = imagecolorallocate($image, 64, 64, 64);
for ($i = 0; $i < 6; $i++) {
    imageline($image, mt_rand(0, 120), mt_rand(0, 40), mt_rand(0, 120), mt_rand(0, 40), $line_color);
}

// 添加噪点
for ($i = 0; $i < 1000; $i++) {
    imagesetpixel($image, mt_rand(0, 120), mt_rand(0, 40), $text_color);
}

// 选择字体
$font = '../ttf/CascadiaMono.ttf'; // 使用普通字体
// 绘制验证码字符
imagettftext($image, 20, 0, 30, 30, $text_color, $font, $captcha_text);

imagepng($image);
imagedestroy($image);
