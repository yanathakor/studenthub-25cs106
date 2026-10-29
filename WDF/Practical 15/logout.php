<?php
require_once 'config.php';

if (!empty($_SESSION['user_id'])) {
    $stmt = mysqli_prepare($conn, "UPDATE users SET remember_token=NULL WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
}
setcookie('remember', '', time() - 3600, '/');
$_SESSION = [];
session_destroy();
session_start();
flash("You have been logged out.");
header("Location: login.php");
exit;
