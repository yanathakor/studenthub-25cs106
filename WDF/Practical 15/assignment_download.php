<?php
require_once 'config.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$st = mysqli_prepare($conn, "SELECT file_name, stored_name FROM assignments WHERE id=?");
mysqli_stmt_bind_param($st, "i", $id);
mysqli_stmt_execute($st);
$a = mysqli_fetch_assoc(mysqli_stmt_get_result($st));

$path = $a ? UPLOAD_DIR . basename($a['stored_name']) : '';
if (!$a || !$a['stored_name'] || !is_file($path)) {
    http_response_code(404);
    die("File not found.");
}
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . str_replace('"', '', $a['file_name']) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
