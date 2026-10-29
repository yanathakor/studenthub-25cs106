
<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
    exit;
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied. Admin only.');
}

$message = '';
$error = '';
$editStudent = null;

if (isset($_GET['message'])) {
    $message = (string) $_GET['message'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'delete') {
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);

            if (!$id) {
                $error = 'Invalid student ID.';
            } else {
                $stmt = $conn->prepare(
                    "DELETE FROM users WHERE id = ? AND role = 'student'"
                );
                $stmt->bind_param('i', $id);
                $stmt->execute();

                $message = $stmt->affected_rows > 0
                    ? 'Student deleted successfully.'
                    : 'Student not found or could not be deleted.';

                $stmt->close();
            }
        } elseif ($action === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $name = trim($_POST['full_name'] ?? '');
            $email = strtolower(trim($_POST['email'] ?? ''));
            $mobile = trim($_POST['mobile'] ?? '');
            $gender = trim($_POST['gender'] ?? '');
            $password = $_POST['password'] ?? '';

            if (
                $name === '' ||
                !filter_var($email, FILTER_VALIDATE_EMAIL) ||
                $mobile === '' ||
                !in_array($gender, ['Male', 'Female', 'Other'], true)
            ) {
                $error = 'Enter valid student details.';
            } elseif ($id === 0 && strlen($password) < 8) {
                $error = 'Password must contain at least 8 characters.';
            } elseif ($password !== '' && strlen($password) > 72) {
                $error = 'Password is too long.';
            } else {
                $stmt = $conn->prepare(
                    'SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1'
                );
                $stmt->bind_param('si', $email, $id);
                $stmt->execute();
                $duplicate = $stmt->get_result()->num_rows > 0;
                $stmt->close();

                if ($duplicate) {
                    $error = 'This email is already registered.';
                } elseif ($id === 0) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $conn->prepare(
                        "INSERT INTO users
                        (full_name, email, mobile, gender, password, role)
                        VALUES (?, ?, ?, ?, ?, 'student')"
                    );
                    $stmt->bind_param(
                        'sssss', $name, $email, $mobile, $gender, $hash
                    );

                    if ($stmt->execute()) {
                        $message = 'Student added successfully.';
                    } else {
                        $error = 'Unable to add student.';
                    }
                    $stmt->close();
                } else {
                    if ($password !== '') {
                        $hash = password_hash($password, PASSWORD_DEFAULT);

                        $stmt = $conn->prepare(
                            "UPDATE users
                            SET full_name=?, email=?, mobile=?, gender=?, password=?
                            WHERE id=? AND role='student'"
                        );
                        $stmt->bind_param(
                            'sssssi', $name, $email, $mobile, $gender, $hash, $id
                        );
                    } else {
                        $stmt = $conn->prepare(
                            "UPDATE users
                            SET full_name=?, email=?, mobile=?, gender=?
                            WHERE id=? AND role='student'"
                        );
                        $stmt->bind_param(
                            'ssssi', $name, $email, $mobile, $gender, $id
                        );
                    }

                    if ($stmt->execute() && $stmt->affected_rows >= 0) {
                        $message = 'Student details updated successfully.';
                    } else {
                        $error = 'Unable to update student.';
                    }
                    $stmt->close();
                }
            }
        }
    }

    if ($error === '') {
        $text = $message;
        header('Location: student_management.php?message=' . urlencode($text));
        exit;
    }
}

if (isset($_GET['edit'])) {
    $id = filter_var($_GET['edit'], FILTER_VALIDATE_INT);

    if ($id) {
        $stmt = $conn->prepare(
            "SELECT id, full_name, email, mobile, gender
             FROM users WHERE id=? AND role='student'"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $editStudent = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

$search = trim($_GET['search'] ?? '');
$genderFilter = $_GET['gender'] ?? '';

$sql = "SELECT id, full_name, email, mobile, gender
        FROM users WHERE role='student'";
$types = '';
$params = [];

if ($search !== '') {
    $sql .= ' AND (full_name LIKE ? OR email LIKE ? OR mobile LIKE ?)';
    $term = '%' . $search . '%';
    $types .= 'sss';
    array_push($params, $term, $term, $term);
}

if (in_array($genderFilter, ['Male', 'Female', 'Other'], true)) {
    $sql .= ' AND gender = ?';
    $types .= 's';
    $params[] = $genderFilter;
}

$sql .= ' ORDER BY id DESC';

$stmt = $conn->prepare($sql);

if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$students = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Management | StudentHub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 25px;
            background: #f3f7f6;
            color: #364958;
        }
        .container {
            max-width: 1100px;
            margin: auto;
        }
        h1 { color: #364958; }
        .panel {
            background: white;
            padding: 22px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 2px 8px #00000012;
        }
        input, select, button {
            padding: 10px;
            margin: 5px 3px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        button, .btn {
            background: #364958;
            color: white;
            border: 0;
            padding: 10px 14px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .delete { background: #b42318; }
        .message { color: #187344; }
        .error { color: #c00; }
        .table-wrap { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th { background: #c9e4ca; }
        .actions { white-space: nowrap; }
    </style>
</head>
<body>
<div class="container">
    <h1>Student Management</h1>
    <p><a class="btn" href="admin.php">Back to Admin Dashboard</a>
       <a class="btn" href="logout.php">Logout</a></p>

    <?php if ($message !== ''): ?>
        <p class="message"><?= e($message) ?></p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <div class="panel">
        <h2><?= $editStudent ? 'Edit Student' : 'Add Student' ?></h2>

        <form method="post" action="student_management.php">
            <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id"
                   value="<?= (int)($editStudent['id'] ?? 0) ?>">

            <input name="full_name" placeholder="Full Name" required
                   maxlength="100"
                   value="<?= e($editStudent['full_name'] ?? '') ?>">

            <input type="email" name="email" placeholder="Email" required
                   maxlength="100"
                   value="<?= e($editStudent['email'] ?? '') ?>">

            <input name="mobile" placeholder="Mobile" required maxlength="20"
                   value="<?= e($editStudent['mobile'] ?? '') ?>">

            <select name="gender" required>
                <option value="">Select Gender</option>
                <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                    <option value="<?= e($g) ?>"
                        <?= (($editStudent['gender'] ?? '') === $g) ? 'selected' : '' ?>>
                        <?= e($g) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="password" name="password"
                   placeholder="<?= $editStudent ? 'New password (optional)' : 'Password (min 8 characters)' ?>"
                   <?= $editStudent ? '' : 'required' ?>
                   minlength="8" maxlength="72" autocomplete="new-password">

            <button type="submit">
                <?= $editStudent ? 'Update Student' : 'Add Student' ?>
            </button>

            <?php if ($editStudent): ?>
                <a class="btn" href="student_management.php">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="panel">
        <h2>Search and Filter Students</h2>
        <form method="get" action="student_management.php">
            <input name="search" placeholder="Search name, email or mobile"
                   value="<?= e($search) ?>">

            <select name="gender">
                <option value="">All genders</option>
                <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                    <option value="<?= e($g) ?>"
                        <?= $genderFilter === $g ? 'selected' : '' ?>>
                        <?= e($g) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Search / Filter</button>
            <a class="btn" href="student_management.php">Reset</a>
        </form>
    </div>

    <div class="panel">
        <h2>Student Records</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Name</th><th>Email</th>
                        <th>Mobile</th><th>Gender</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($students->num_rows > 0): ?>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$student['id'] ?></td>
                            <td><?= e($student['full_name']) ?></td>
                            <td><?= e($student['email']) ?></td>
                            <td><?= e($student['mobile']) ?></td>
                            <td><?= e($student['gender']) ?></td>
                            <td class="actions">
                                <a class="btn"
                                   href="student_management.php?edit=<?= (int)$student['id'] ?>">
                                   Edit
                                </a>

                                <form method="post" action="student_management.php"
                                      style="display:inline"
                                      onsubmit="return confirm('Delete this student?')">
                                    <input type="hidden" name="csrf"
                                           value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id"
                                           value="<?= (int)$student['id'] ?>">
                                    <button class="delete" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No students found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>