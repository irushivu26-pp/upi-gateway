<?php
require_once __DIR__ . '/../config.php';

function connect_database() {
    $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if (!$con) {
        die("Database Connection failed: " . mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf8mb4');
    mysqli_query($con, "SET time_zone = '+05:30'");
    return $con;
}
?>
