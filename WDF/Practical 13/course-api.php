
<?php
require 'config.php';
requireLogin();

header('Content-Type: application/json; charset=utf-8');

function respond($success, $message = '', $data = null, $status = 200) {
    http_response_code($status);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'], true)) {
    header('Allow: GET, POST, PUT, DELETE');
    respond(false, 'Method not allowed.', null, 405);
}

if ($method === 'GET') {
    $result = $conn->query(
        'SELECT id, name, description, icon FROM courses ORDER BY id DESC'
    );

    if (!$result) {
        respond(false, 'Unable to fetch courses.', null, 500);
    }

    respond(true, 'Courses fetched successfully.', $result->fetch_all(MYSQLI_ASSOC));
}

if (!$isAdmin) {
    respond(false, 'Only admin can add, update or delete courses.', null, 403);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    respond(false, 'Invalid JSON data.', null, 400);
}

if ($method === 'POST') {
    $name = trim($input['name'] ?? '');
    $description = trim($input['description'] ?? '');
    $icon = trim($input['icon'] ?? 'fas fa-book');

    if ($name === '' || $description === '') {
        respond(false, 'Course name and description are required.', null, 422);
    }

    if (strlen($name) > 150 || strlen($description) > 500 ||
        strlen($icon) > 100) {
        respond(false, 'Input is too long.', null, 422);
    }

    $stmt = $conn->prepare(
        'INSERT INTO courses (name, description, icon) VALUES (?, ?, ?)'
    );
    $stmt->bind_param('sss', $name, $description, $icon);

    if (!$stmt->execute()) {
        $stmt->close();
        respond(false, 'Could not add course.', null, 500);
    }

    $id = $conn->insert_id;
    $stmt->close();

    respond(true, 'Course added successfully.', ['id' => $id], 201);
}

$id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    respond(false, 'Valid course ID is required.', null, 422);
}

if ($method === 'PUT') {
    $name = trim($input['name'] ?? '');
    $description = trim($input['description'] ?? '');
    $icon = trim($input['icon'] ?? 'fas fa-book');

    if ($name === '' || $description === '') {
        respond(false, 'Course name and description are required.', null, 422);
    }

    if (strlen($name) > 150 || strlen($description) > 500 ||
        strlen($icon) > 100) {
        respond(false, 'Input is too long.', null, 422);
    }

    $stmt = $conn->prepare(
        'UPDATE courses SET name = ?, description = ?, icon = ? WHERE id = ?'
    );
    $stmt->bind_param('sssi', $name, $description, $icon, $id);

    if (!$stmt->execute()) {
        $stmt->close();
        respond(false, 'Could not update course.', null, 500);
    }

    $changed = $stmt->affected_rows;
    $stmt->close();

    if ($changed === 0) {
        $check = $conn->prepare('SELECT id FROM courses WHERE id = ?');
        $check->bind_param('i', $id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$exists) {
            respond(false, 'Course not found.', null, 404);
        }
    }

    respond(true, 'Course updated successfully.');
}

if ($method === 'DELETE') {
    $stmt = $conn->prepare('DELETE FROM courses WHERE id = ?');
    $stmt->bind_param('i', $id);

    if (!$stmt->execute()) {
        $stmt->close();
        respond(false, 'Could not delete course.', null, 500);
    }

    $deleted = $stmt->affected_rows;
    $stmt->close();

    if (!$deleted) {
        respond(false, 'Course not found.', null, 404);
    }

    respond(true, 'Course deleted successfully.');
}
?>