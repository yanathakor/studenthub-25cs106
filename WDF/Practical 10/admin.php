<?php
require 'config.php';
requireRole(['admin']);
$activePage = 'admin';

$roles   = ['student', 'teacher', 'admin'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId  = (int)($_POST['user_id'] ?? 0);
    $newRole = $_POST['role'] ?? '';

    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $message = 'Invalid request.';
    } elseif (!in_array($newRole, $roles, true)) {
        $message = 'Invalid role.';
    } elseif ($userId === (int)$_SESSION['user_id']) {
        $message = 'You cannot change your own role.';
    } else {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $newRole, $userId);
        $stmt->execute();
        $stmt->close();
        $message = 'Role updated.';
    }
}

$users = $conn->query("SELECT id, full_name, email, role FROM users ORDER BY id")->fetch_all(MYSQLI_ASSOC);
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

</head>

<body>

<div class="container">

    <?php include 'sidebar.php'; ?>

    <div class="main">

        <div class="header">

            <div class="search">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input type="text" placeholder="Search">

            </div>

            <a href="profile.php" class="profile">
                <i class="fa-solid fa-user"></i>
            </a>

        </div>

        <h1>Manage Users</h1>

        <?php if ($message !== ''): ?>
            <p style="margin-bottom:15px;"><?= e($message) ?></p>
        <?php endif; ?>

        <div class="result-card">

            <table>

                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>

                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int)$u['id'] ?></td>
                        <td><?= e($u['full_name']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td>
                            <form method="POST" action="admin.php" style="display:flex;gap:8px;">
                                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                <select name="role"<?= ((int)$u['id'] === (int)$_SESSION['user_id']) ? ' disabled' : '' ?>>
                                    <?php foreach ($roles as $r): ?>
                                        <option value="<?= e($r) ?>"<?= $u['role'] === $r ? ' selected' : '' ?>><?= e(ucfirst($r)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                                    <button type="submit">Update</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>

        </div>

    </div>

</div>

</body>
</html>