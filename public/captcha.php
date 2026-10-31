<?php
session_start();
$code = random_int(100000, 999999);
$_SESSION['captcha'] = $code;
header('Content-type: image/png');
$image = imagecreate(70, 30);
$bg = imagecolorallocate($image, 255, 255, 255); // white
$text_color = imagecolorallocate($image, 0, 0, 0); // black
imagestring($image, 5, 10, 5, $code, $text_color);
imagepng($image);
imagedestroy($image);
?>