<?php
require 'config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$info  = '';
$email = '';
$selectedRole = '';
$allowedRoles = ['student', 'teacher', 'admin'];

if (!empty($_SESSION['timed_out'])) {
    $info = 'Your session expired due to inactivity. Please log in again.';
    unset($_SESSION['timed_out']);
}

if (isset($_GET['registered'])) {
    $info = 'Registration successful. Please log in.';
}

if (isset($_GET['logout'])) {
    $info = 'You have been logged out.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email        = strtolower(trim($_POST['txtname'] ?? ''));
        $password     = $_POST['txtpassword'] ?? '';
        $selectedRole = $_POST['role'] ?? '';
        $remember     = !empty($_POST['remember']);

        if ($email === '' || $password === '') {
            $error = 'Please enter your email and password.';
        } elseif (!in_array($selectedRole, $allowedRoles, true)) {
            $error = 'Please select a role.';
        } else {
            $stmt = $conn->prepare("SELECT id, full_name, role, password, failed_attempts, (locked_until IS NOT NULL AND locked_until > NOW()) AS is_locked FROM users WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($user && !$user['is_locked'] && (int)$user['failed_attempts'] >= MAX_ATTEMPTS) {
                $userId = (int)$user['id'];
                $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $stmt->close();
                $user['failed_attempts'] = 0;
            }

            if ($user && $user['is_locked']) {
                $error = 'Too many failed attempts. Please try again after ' . LOCK_MINUTES . ' minutes.';
            } elseif ($user && password_verify($password, $user['password'])) {

                if ($user['role'] !== $selectedRole) {
                    $error = 'Invalid email, password or role.';
                } else {
                    $userId = (int)$user['id'];

                    $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $stmt->close();

                    $conn->query("DELETE FROM remember_tokens WHERE expires_at < NOW()");

                    loginUser($userId, $user['full_name'], $user['role']);

                    if ($remember) {
                        createRememberToken($conn, $userId);
                    } else {
                        clearRememberCookie();
                    }

                    redirect('index.php');
                }

            } else {
                if ($user) {
                    $userId   = (int)$user['id'];
                    $max      = MAX_ATTEMPTS;
                    $minutes  = LOCK_MINUTES;
                    $stmt = $conn->prepare("UPDATE users SET failed_attempts = failed_attempts + 1, locked_until = IF(failed_attempts >= ?, DATE_ADD(NOW(), INTERVAL ? MINUTE), locked_until) WHERE id = ?");
                    $stmt->bind_param("iii", $max, $minutes, $userId);
                    $stmt->execute();
                    $stmt->close();
                }
                $error = 'Invalid email, password or role.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Login</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>

    <div class="container">

        <div class="left">
            <h1>STUDENTHUB</h1>

            <h2>Welcome Back!</h2>

            <p>
                Manage your courses, assignments,<br>
                attendance and results in one place.
            </p>
        </div>

        <div class="right">

            <form method="POST" action="login.php" onsubmit="return validateLoginForm()">

                <h2>Login</h2>

                <div>

                    <?php if ($error !== ''): ?>
                        <p style="color:red;margin-bottom:10px;"><?= e($error) ?></p>
                    <?php endif; ?>

                    <?php if ($info !== ''): ?>
                        <p style="color:green;margin-bottom:10px;"><?= e($info) ?></p>
                    <?php endif; ?>

                    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

                    <input type="text"
                           id="txtname"
                           name="txtname"
                           value="<?= e($email) ?>"
                           placeholder="Email"
                           autocomplete="username">

                    <span id="s"></span>

                    <input type="password"
                           id="txtpassword"
                           name="txtpassword"
                           placeholder="Password"
                           autocomplete="current-password">

                    <span id="s1"></span>

                    <select id="role"
                            name="role"
                            style="width:100%;padding:12px;margin-top:15px;border:1px solid #ccc;border-radius:6px;font-size:15px;">

                        <option value="">Select Role</option>
                        <option value="student"<?= $selectedRole === 'student' ? ' selected' : '' ?>>Student</option>
                        <option value="admin"<?= $selectedRole === 'admin' ? ' selected' : '' ?>>Admin</option>

                    </select>

                    <span id="s2"></span>

                    <div class="show-password">

                        <input type="checkbox" id="showPassword">

                        <label for="showPassword">
                            Show Password
                        </label>

                    </div>

                    <div class="show-password">

                        <input type="checkbox" id="remember" name="remember" value="1">

                        <label for="remember">
                            Remember me
                        </label>

                    </div>

                    <button type="submit" class="button">
                        Login
                    </button>

                    <a href="#">Forgot Password?</a>

                    <br><br>

                    <p>
                        Don't have an account?
                        <a href="register.html">Register</a>
                    </p>

                </div>

            </form>

        </div>

    </div>

    <script src="login.js"></script>

</body>

</html>