<?php

date_default_timezone_set('Asia/Kolkata');
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

$serverName = $_SERVER['SERVER_NAME'];


// Combine the components to get the full URL
$murl = $protocol . '://' . $serverName;

function RandomNumber($length) {
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= mt_rand(0, 9);
    }
    return $str;
}
function GenRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateUniqueToken() {
    $token = time() . bin2hex(random_bytes(16)) . rand(1, 50);
    return hash('sha256', $token);
}