<?php
require_once 'config.php';
if (!empty($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $conf  = $_POST['confirm'] ?? '';

    if (strlen($name) < 3) $errors[] = "Name must be at least 3 characters.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email.";
    if (strlen($pass) < 8 || !preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass))
        $errors[] = "Password must be 8+ characters with letters and numbers.";
    if ($pass !== $conf) $errors[] = "Passwords do not match.";

    if (!$errors) {
        $c = mysqli_prepare($conn, "SELECT id FROM users WHERE email=?");
        mysqli_stmt_bind_param($c, "s", $email);
        mysqli_stmt_execute($c);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($c))) {
            $errors[] = "Email already registered.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $i = mysqli_prepare($conn, "INSERT INTO users (name,email,password,role) VALUES (?,?,?,'student')");
            mysqli_stmt_bind_param($i, "sss", $name, $email, $hash);
            mysqli_stmt_execute($i);
            flash("Registration successful. Please log in.");
            header("Location: login.php"); exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register - StudentHub</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
<div class="auth-card">
  <h1>🎓 StudentHub</h1>
  <p class="muted">Create a student account</p>
  <?php foreach ($errors as $er) echo '<div class="alert error">' . e($er) . '</div>'; ?>
  <form method="post" id="registerForm" novalidate>
    <?= csrf_field() ?>
    <label>Full name</label>
    <input type="text" name="name" id="name" value="<?= e($_POST['name'] ?? '') ?>" required>
    <label>Email</label>
    <input type="email" name="email" id="email" value="<?= e($_POST['email'] ?? '') ?>" required>
    <label>Password</label>
    <input type="password" name="password" id="password" required>
    <label>Confirm password</label>
    <input type="password" name="confirm" id="confirm" required>
    <button type="submit" class="btn">Register</button>
  </form>
  <p class="muted center">Already registered? <a href="login.php">Login</a></p>
</div>
<script src="script.js"></script>
</body>
</html>
