<?php
session_start();

// Generate a random CAPTCHA code
$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$captchaCode = '';
for ($i = 0; $i < 6; $i++) {
    $captchaCode .= $characters[rand(0, strlen($characters) - 1)];
}

// Store CAPTCHA code in session
$_SESSION['captcha'] = $captchaCode;

// Create the CAPTCHA image
$image = imagecreate(100, 40);
$bgColor = imagecolorallocate($image, 255, 255, 255); // White background
$textColor = imagecolorallocate($image, 0, 0, 0); // Black text
imagestring($image, 5, 10, 10, $captchaCode, $textColor);

// Output the image
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
exit;
