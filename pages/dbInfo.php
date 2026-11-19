<?php
require_once __DIR__ . '/../config.php';

if (!function_exists('connect_database')) {
    function connect_database() {
        mysqli_report(MYSQLI_REPORT_OFF);
        $con = null;
        for ($i = 0; $i < 5; $i++) {
            $con = @mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
            if ($con) break;
            usleep(400000); // 400ms delay between retries
        }
        if (!$con) {
            die("Database Connection failed: " . mysqli_connect_error());
        }
        mysqli_set_charset($con, 'utf8mb4');
        mysqli_query($con, "SET time_zone = '+05:30'");
        return $con;
    }
}
?>