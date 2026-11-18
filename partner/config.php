<?php
require_once __DIR__ . '/../config.php';

$servername = DB_HOST;
$username = DB_USERNAME;
$password = DB_PASSWORD;
$database = DB_NAME;

// Ensure connection exists
if (!isset($conn) || !$conn) {
    $conn = @mysqli_connect($servername, $username, $password, $database);
    if ($conn) {
        mysqli_set_charset($conn, 'utf8mb4');
        mysqli_query($conn, "SET time_zone = '+05:30'");
    }
}

$server = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
if (!isset($murl)) {
    $murl = $protocol . '://' . $server;
}
?>
