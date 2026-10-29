<?php
// Copy this file to config.php and fill in your details.
$host = "localhost";
$user = "root";
$pass = "";
$db   = "studenthub";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
