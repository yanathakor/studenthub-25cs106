<?php
// JSON API: course-api.php            -> all courses
//           course-api.php?q=web      -> search by name/code
//           course-api.php?id=1       -> single course
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Login required']);
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $st = mysqli_prepare($conn, "SELECT id,code,name,credits,instructor FROM courses WHERE id=?");
    mysqli_stmt_bind_param($st, "i", $id);
} elseif (!empty($_GET['q'])) {
    $like = '%' . $_GET['q'] . '%';
    $st = mysqli_prepare($conn, "SELECT id,code,name,credits,instructor FROM courses WHERE name LIKE ? OR code LIKE ?");
    mysqli_stmt_bind_param($st, "ss", $like, $like);
} else {
    $st = mysqli_prepare($conn, "SELECT id,code,name,credits,instructor FROM courses ORDER BY code");
}
mysqli_stmt_execute($st);
$rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);
echo json_encode(['success' => true, 'count' => count($rows), 'data' => $rows]);
