<?php
require 'config.php';
requireLogin();

$userId = (int)($_SESSION['user_id'] ?? 0);
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    exit('Invalid assignment ID.');
}

if ($isAdmin) {
    $stmt = $conn->prepare(
        'SELECT file_name FROM assignment_submissions WHERE id = ?'
    );
    $stmt->bind_param('i', $id);
} else {
    $stmt = $conn->prepare(
        'SELECT file_name FROM assignment_submissions
         WHERE id = ? AND student_id = ?'
    );
    $stmt->bind_param('ii', $id, $userId);
}

$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    http_response_code(404);
    exit('Assignment not found or access denied.');
}

$filePath = __DIR__ . '/assignment_uploads/' . basename($row['file_name']);

if (!is_file($filePath)) {
    http_response_code(404);
    exit('File not found.');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="assignment-' . (int)$id . '.' . pathinfo($filePath, PATHINFO_EXTENSION) . '"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;