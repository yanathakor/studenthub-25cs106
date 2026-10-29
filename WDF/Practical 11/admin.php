
<?php
require 'config.php';
requireRole(['admin']);

$activePage = 'admin';

$roles = ['student', 'teacher', 'admin'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = filter_var($_POST['user_id'] ?? '', FILTER_VALIDATE_INT);
    $newRole = $_POST['role'] ?? '';

    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } elseif (!$userId || !in_array($newRole, $roles, true)) {
        $error = 'Invalid user or role.';
    } elseif ($userId === (int) $_SESSION['user_id']) {
        $error = 'You cannot change your own role.';
    } else {
        $stmt = $conn->prepare(
            'UPDATE users SET role = ? WHERE id = ?'
        );
        $stmt->bind_param('si', $newRole, $userId);

        if ($stmt->execute()) {
            $message = $stmt->affected_rows > 0
                ? 'User role updated successfully.'
                : 'Role unchanged or user not found.';
        } else {
            $error = 'Failed to update user role.';
        }

        $stmt->close();
    }
}

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $term = '%' . $search . '%';

    $stmt = $conn->prepare(
        'SELECT id, full_name, email, role
         FROM users
         WHERE full_name LIKE ? OR email LIKE ?
         ORDER BY id'
    );

    $stmt->bind_param('ss', $term, $term);
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $result = $conn->query(
        'SELECT id, full_name, email, role
         FROM users
         ORDER BY id'
    );

    $users = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Admin</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

    <style>
        .admin-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 20px 0;
        }

        .admin-btn {
            display: inline-block;
            padding: 11px 16px;
            border: none;
            border-radius: 6px;
            background: #364958;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .admin-btn:hover {
            background: #55828b;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-form input {
            min-width: 100px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .admin-table-wrapper {
            overflow-x: auto;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .admin-table th,
        .admin-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .admin-table th {
            background: #c9e4ca;
            color: #364958;
        }

        .role-form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }

        .role-form select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .status-message {
            padding: 12px;
            margin: 12px 0;
            background: #e2f5e8;
            color: #187344;
            border-radius: 5px;
        }

        .error-message {
            padding: 12px;
            margin: 12px 0;
            background: #fde8e7;
            color: #a61b1b;
            border-radius: 5px;
        }

        @media (max-width: 600px) {
            .search-form {
                flex-wrap: wrap;
            }

            .admin-table th,
            .admin-table td {
                padding: 8px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <?php include 'sidebar.php'; ?>

    <div class="main">

        <div class="header">
            <form method="GET" action="admin.php" class="search-form">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="search"
                    placeholder="Search name or email"
                    value="<?= e($search) ?>">

                <button type="submit" class="admin-btn">Search</button>

                <?php if ($search !== ''): ?>
                    <a href="admin.php" class="admin-btn">Reset</a>
                <?php endif; ?>
            </form>

            <a href="profile.php" class="profile" aria-label="Profile">
                <i class="fa-solid fa-user"></i>
            </a>
        </div>

        <h1>Admin Dashboard</h1>

        <div class="admin-actions">
            <a href="student_management.php" class="admin-btn">
                <i class="fa-solid fa-user-graduate"></i>
                Student Management (CRUD)
            </a>
        </div>

        <?php if ($message !== ''): ?>
            <p class="status-message"><?= e($message) ?></p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p class="error-message"><?= e($error) ?></p>
        <?php endif; ?>

        <div class="result-card">
            <h2>Manage Users</h2>

            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (count($users) > 0): ?>

                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= (int) $u['id'] ?></td>

                                <td><?= e($u['full_name']) ?></td>

                                <td><?= e($u['email']) ?></td>

                                <td>
                                    <form
                                        method="POST"
                                        action="admin.php<?= $search !== '' ? '?search=' . urlencode($search) : '' ?>"
                                        class="role-form">

                                        <input
                                            type="hidden"
                                            name="csrf"
                                            value="<?= e(csrfToken()) ?>">

                                        <input
                                            type="hidden"
                                            name="user_id"
                                            value="<?= (int) $u['id'] ?>">

                                        <select
                                            name="role"
                                            aria-label="Role for <?= e($u['full_name']) ?>"
                                            <?= (int) $u['id'] === (int) $_SESSION['user_id'] ? 'disabled' : '' ?>>

                                            <?php foreach ($roles as $r): ?>
                                                <option
                                                    value="<?= e($r) ?>"
                                                    <?= $u['role'] === $r ? 'selected' : '' ?>>
                                                    <?= e(ucfirst($r)) ?>
                                                </option>
                                            <?php endforeach; ?>

                                        </select>

                                        <?php if ((int) $u['id'] !== (int) $_SESSION['user_id']): ?>
                                            <button type="submit" class="admin-btn">
                                                Update
                                            </button>
                                        <?php else: ?>
                                            <span>Current account</span>
                                        <?php endif; ?>

                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="4">No users found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

</body>
</html>