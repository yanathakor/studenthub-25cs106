<?php
require_once 'config.php';
try_remember_login($conn);
if (!empty($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    // simple brute-force protection
    if (($_SESSION['lock_until'] ?? 0) > time()) {
        $error = "Too many attempts. Try again in a few minutes.";
    } else {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $stmt = mysqli_prepare($conn, "SELECT id,name,password,role FROM users WHERE email=?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['attempts'] = 0;
            start_user_session($user);
            if (!empty($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                $hash  = hash('sha256', $token);
                $u = mysqli_prepare($conn, "UPDATE users SET remember_token=? WHERE id=?");
                mysqli_stmt_bind_param($u, "si", $hash, $user['id']);
                mysqli_stmt_execute($u);
                setcookie('remember', $user['id'] . ':' . $token, [
                    'expires' => time() + 86400 * REMEMBER_DAYS, 'path' => '/',
                    'httponly' => true, 'samesite' => 'Lax'
                ]);
            }
            header("Location: index.php"); exit;
        } else {
            $_SESSION['attempts'] = ($_SESSION['attempts'] ?? 0) + 1;
            if ($_SESSION['attempts'] >= 5) {
                $_SESSION['lock_until'] = time() + 300;
                $_SESSION['attempts'] = 0;
            }
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - StudentHub</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
<div class="auth-card">
  <h1>🎓 StudentHub</h1>
  <p class="muted">Sign in to your account</p>
  <?php show_flash(); if ($error) echo '<div class="alert error">' . e($error) . '</div>'; ?>
  <form method="post" id="loginForm" novalidate>
    <?= csrf_field() ?>
    <label>Email</label>
    <input type="email" name="email" id="email" required>
    <label>Password</label>
    <input type="password" name="password" id="password" required>
    <label class="check"><input type="checkbox" name="remember"> Remember me for 30 days</label>
    <button type="submit" class="btn">Login</button>
  </form>
  <p class="muted center">No account? <a href="register.php">Register</a></p>
</div>
<script src="script.js"></script>
</body>
</html>
