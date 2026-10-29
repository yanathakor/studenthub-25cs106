
<?php
require 'config.php';
requireRole(['admin']);

$activePage = 'admin';
$message = '';
$error = '';
$roles = ['student', 'teacher', 'admin'];

function writeAuditLog($conn, $adminId, $action, $details)
{
    $stmt = $conn->prepare(
        'INSERT INTO audit_logs (admin_id, action, details) VALUES (?, ?, ?)'
    );

    if ($stmt) {
        $stmt->bind_param('iss', $adminId, $action, $details);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log('Audit log prepare failed: ' . $conn->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = filter_var($_POST['user_id'] ?? '', FILTER_VALIDATE_INT);
    $newRole = $_POST['role'] ?? '';

    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } elseif (!$userId || !in_array($newRole, $roles, true)) {
        $error = 'Invalid user or role.';
    } elseif ($userId === (int)$_SESSION['user_id']) {
        $error = 'You cannot change your own role.';
    } else {
        $stmt = $conn->prepare(
            'SELECT full_name, role FROM users WHERE id = ?'
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $targetUser = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$targetUser) {
            $error = 'User not found.';
        } elseif ($targetUser['role'] === $newRole) {
            $message = 'The user already has this role.';
        } else {
            $oldRole = $targetUser['role'];

            $stmt = $conn->prepare(
                'UPDATE users SET role = ? WHERE id = ?'
            );
            $stmt->bind_param('si', $newRole, $userId);

            if ($stmt->execute()) {
                $message = 'User role updated successfully.';

                $auditAction = ($oldRole === 'student' || $newRole === 'student')
                    ? 'STUDENT_UPDATED'
                    : 'ROLE_UPDATED';

                $auditDetails = 'Admin ' . ($_SESSION['user_name'] ?? 'Admin') .
                    ' changed user ID ' . $userId . ' (' .
                    $targetUser['full_name'] . ') role from ' .
                    $oldRole . ' to ' . $newRole . '.';

                writeAuditLog(
                    $conn,
                    (int)$_SESSION['user_id'],
                    $auditAction,
                    $auditDetails
                );
            } else {
                $error = 'Could not update user role.';
            }

            $stmt->close();
        }
    }
}

function getCount($conn, $sql)
{
    $result = $conn->query($sql);

    if (!$result) {
        return 0;
    }

    $row = $result->fetch_row();
    return (int)($row[0] ?? 0);
}

$totalUsers = getCount($conn, 'SELECT COUNT(*) FROM users');
$totalStudents = getCount(
    $conn,
    "SELECT COUNT(*) FROM users WHERE role = 'student'"
);

$eventCount = 0;
$tableCheck = $conn->query("SHOW TABLES LIKE 'events'");

if ($tableCheck && $tableCheck->num_rows > 0) {
    $eventCount = getCount($conn, 'SELECT COUNT(*) FROM events');
}

$latestResult = $conn->query(
    'SELECT id, full_name, email, role, created_at
     FROM users
     ORDER BY created_at DESC, id DESC
     LIMIT 5'
);

$latestUsers = $latestResult
    ? $latestResult->fetch_all(MYSQLI_ASSOC)
    : [];

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $term = '%' . $search . '%';

    $stmt = $conn->prepare(
        'SELECT id, full_name, email, role
         FROM users
         WHERE full_name LIKE ? OR email LIKE ?
         ORDER BY id DESC'
    );
    $stmt->bind_param('ss', $term, $term);
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $result = $conn->query(
        'SELECT id, full_name, email, role
         FROM users
         ORDER BY id DESC'
    );
    $users = $result
        ? $result->fetch_all(MYSQLI_ASSOC)
        : [];
}

$logsResult = $conn->query(
    'SELECT a.action, a.details, a.created_at, u.full_name
     FROM audit_logs a
     LEFT JOIN users u ON u.id = a.admin_id
     ORDER BY a.id DESC
     LIMIT 10'
);

$auditLogs = $logsResult
    ? $logsResult->fetch_all(MYSQLI_ASSOC)
    : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 18px;
            margin: 24px 0;
        }

        .stat-card {
            padding: 22px;
            border-radius: 12px;
            background: #c9e4ca;
            color: #364958;
        }

        .stat-card i {
            font-size: 25px;
            margin-bottom: 12px;
        }

        .stat-card h3 {
            margin: 8px 0;
            font-size: 28px;
        }

        .admin-section {
            background: #fff;
            padding: 20px;
            margin: 22px 0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }

        .admin-table-wrapper {
            overflow-x: auto;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .admin-table th, .admin-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .admin-table th {
            background: #c9e4ca;
            color: #364958;
        }

        .admin-btn {
            padding: 9px 13px;
            background: #364958;
            color: #fff;
            border: 0;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
        }

        .role-form, .search-form {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .role-form select, .search-form input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .status-message, .error-message {
            padding: 12px;
            margin: 12px 0;
            border-radius: 6px;
        }

        .status-message {
            background: #e2f5e8;
            color: #187344;
        }

        .error-message {
            background: #fde8e7;
            color: #a61b1b;
        }

        .audit-details {
            overflow-wrap: anywhere;
        }

        @media (max-width: 600px) {
            .admin-section { padding: 12px; }
            .admin-table th, .admin-table td { padding: 8px; }
        }
    </style>
</head>
<body>
<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main">
        <div class="header">
            <form method="GET" action="admin.php" class="search-form">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" name="search"
                       placeholder="Search users"
                       value="<?= e($search) ?>">
                <button class="admin-btn" type="submit">Search</button>
                <?php if ($search !== ''): ?>
                    <a class="admin-btn" href="admin.php">Reset</a>
                <?php endif; ?>
            </form>

            <a href="profile.php" class="profile" aria-label="Profile">
                <i class="fa-solid fa-user"></i>
            </a>
        </div>

        <h1>Admin Dashboard</h1>
        <p>Welcome, <?= e($_SESSION['user_name'] ?? 'Admin') ?>.</p>

        <?php if ($message !== ''): ?>
            <p class="status-message" role="status"><?= e($message) ?></p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p class="error-message" role="alert"><?= e($error) ?></p>
        <?php endif; ?>

        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fa-solid fa-users"></i>
                <p>Total Users</p>
                <h3><?= $totalUsers ?></h3>
            </div>

            <div class="stat-card">
                <i class="fa-solid fa-user-graduate"></i>
                <p>Students</p>
                <h3><?= $totalStudents ?></h3>
            </div>

            <div class="stat-card">
                <i class="fa-solid fa-calendar-days"></i>
                <p>Events</p>
                <h3><?= $eventCount ?></h3>
            </div>
        </div>

        <div class="admin-actions">
            <a href="student_management.php" class="admin-btn">
                <i class="fa-solid fa-user-gear"></i>
                Student Management
            </a>
        </div>

        <section class="admin-section">
            <h2>Latest Registrations</h2>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!$latestUsers): ?>
                        <tr><td colspan="5">No registrations found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($latestUsers as $u): ?>
                            <tr>
                                <td><?= (int)$u['id'] ?></td>
                                <td><?= e($u['full_name']) ?></td>
                                <td><?= e($u['email']) ?></td>
                                <td><?= e(ucfirst($u['role'])) ?></td>
                                <td><?= e($u['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-section">
            <h2>Manage Users and Roles</h2>
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
                    <?php if (!$users): ?>
                        <tr><td colspan="4">No users found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= (int)$u['id'] ?></td>
                                <td><?= e($u['full_name']) ?></td>
                                <td><?= e($u['email']) ?></td>
                                <td>
                                    <form method="POST"
                                          action="admin.php<?= $search !== '' ? '?search=' . urlencode($search) : '' ?>"
                                          class="role-form">
                                        <input type="hidden" name="csrf"
                                               value="<?= e(csrfToken()) ?>">
                                        <input type="hidden" name="user_id"
                                               value="<?= (int)$u['id'] ?>">
                                        <select name="role"
                                                aria-label="Role for <?= e($u['full_name']) ?>"
                                                <?= (int)$u['id'] === (int)$_SESSION['user_id'] ? 'disabled' : '' ?>>
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?= e($role) ?>"
                                                    <?= $u['role'] === $role ? 'selected' : '' ?>>
                                                    <?= e(ucfirst($role)) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                                            <button class="admin-btn" type="submit">Update</button>
                                        <?php else: ?>
                                            <span>Current account</span>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-section">
            <h2>Audit Log — Recent Admin Actions</h2>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Date and Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!$auditLogs): ?>
                        <tr><td colspan="4">No audit activity recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($auditLogs as $log): ?>
                            <tr>
                                <td><?= e($log['full_name'] ?? 'Deleted user') ?></td>
                                <td><?= e($log['action']) ?></td>
                                <td class="audit-details"><?= e($log['details']) ?></td>
                                <td><?= e($log['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>