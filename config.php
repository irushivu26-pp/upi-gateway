<?php
/**
 * Master Gateway Configuration
 * QR Code UPI Payment Gateway (PhonePe, Paytm, BharatPe, HDFC)
 */

// Error handling - set to 0 in production, E_ALL for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

// Database Configuration
// Edit these credentials to match your MySQL database
$default_db_user = (PHP_OS_FAMILY === 'Windows') ? 'root' : 'gateway_user';
$default_db_pass = (PHP_OS_FAMILY === 'Windows') ? '' : 'gateway_pass123';
$default_db_host = (PHP_OS_FAMILY === 'Windows') ? 'localhost' : '127.0.0.1';

if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: $default_db_host);
if (!defined('DB_USERNAME')) define('DB_USERNAME', getenv('DB_USER') ?: $default_db_user);
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', getenv('DB_PASS') !== false ? getenv('DB_PASS') : $default_db_pass);
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'upi_gateway');

// Dynamic Server, Protocol & Base Path Detection
$is_https = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1)) || 
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

$protocol = $is_https ? 'https' : 'http';
$serverName = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
$server = $serverName;

// Detect subdirectory path if running inside a subfolder (e.g. /gateway)
$doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? strtolower(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']) ?: $_SERVER['DOCUMENT_ROOT'])) : '';
$app_dir = strtolower(str_replace('\\', '/', realpath(__DIR__) ?: __DIR__));
$base_path = '';
if ($doc_root && strpos($app_dir, $doc_root) === 0) {
    $sub = substr($app_dir, strlen($doc_root));
    $base_path = '/' . trim($sub, '/');
    if ($base_path === '/') {
        $base_path = '';
    }
}

$murl = $protocol . '://' . $serverName . $base_path;
$cxrurl = $serverName;
$cxrteamurl = $serverName;

// Global mysqli connection instance with safe retry for cold-starts
mysqli_report(MYSQLI_REPORT_OFF);
if (!isset($conn) || !$conn) {
    for ($i = 0; $i < 4; $i++) {
        $conn = @mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if ($conn) break;
        usleep(300000); // 300ms delay between retries
    }
    if ($conn) {
        mysqli_set_charset($conn, 'utf8mb4');
        mysqli_query($conn, "SET time_zone = '+05:30'");
    }
}

// Global PDO connection helper
function get_pdo_connection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USERNAME, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}
?>
