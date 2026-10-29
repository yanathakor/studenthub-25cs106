<?php
require_once 'config.php';
require_login();
$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (isset($_POST['update_profile'])) {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if (strlen($name) < 3 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash("Enter a valid name and email.", "error");
        } else {
            $c = mysqli_prepare($conn, "SELECT id FROM users WHERE email=? AND id<>?");
            mysqli_stmt_bind_param($c, "si", $email, $uid);
            mysqli_stmt_execute($c);
            if (mysqli_fetch_assoc(mysqli_stmt_get_result($c))) {
                flash("That email is already used.", "error");
            } else {
                $u = mysqli_prepare($conn, "UPDATE users SET name=?, email=? WHERE id=?");
                mysqli_stmt_bind_param($u, "ssi", $name, $email, $uid);
                mysqli_stmt_execute($u);
                $_SESSION['name'] = $name;
                flash("Profile updated.");
            }
        }
    } elseif (isset($_POST['change_password'])) {
        $cur = $_POST['current'] ?? ''; $new = $_POST['new'] ?? '';
        $s = mysqli_prepare($conn, "SELECT password FROM users WHERE id=?");
        mysqli_stmt_bind_param($s, "i", $uid);
        mysqli_stmt_execute($s);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        if (!password_verify($cur, $row['password'])) {
            flash("Current password is wrong.", "error");
        } elseif (strlen($new) < 8 || !preg_match('/[A-Za-z]/', $new) || !preg_match('/\d/', $new)) {
            flash("New password must be 8+ characters with letters and numbers.", "error");
        } else {
            $h = password_hash($new, PASSWORD_DEFAULT);
            $u = mysqli_prepare($conn, "UPDATE users SET password=?, remember_token=NULL WHERE id=?");
            mysqli_stmt_bind_param($u, "si", $h, $uid);
            mysqli_stmt_execute($u);
            flash("Password changed.");
        }
    }
    header("Location: profile.php"); exit;
}

$s = mysqli_prepare($conn, "SELECT name,email,role,created_at FROM users WHERE id=?");
mysqli_stmt_bind_param($s, "i", $uid);
mysqli_stmt_execute($s);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($s));

$title = "My Profile";
include 'sidebar.php';
?>
<div class="grid2">
  <div class="panel">
    <h3>Profile details</h3>
    <form method="post">
      <?= csrf_field() ?>
      <label>Name</label><input type="text" name="name" value="<?= e($user['name']) ?>" required>
      <label>Email</label><input type="email" name="email" value="<?= e($user['email']) ?>" required>
      <label>Role</label><input type="text" value="<?= e(ucfirst($user['role'])) ?>" disabled>
      <label>Member since</label><input type="text" value="<?= e($user['created_at']) ?>" disabled>
      <button class="btn" name="update_profile">Save changes</button>
    </form>
  </div>
  <div class="panel">
    <h3>Change password</h3>
    <form method="post">
      <?= csrf_field() ?>
      <label>Current password</label><input type="password" name="current" required>
      <label>New password</label><input type="password" name="new" required>
      <button class="btn" name="change_password">Update password</button>
    </form>
  </div>
</div>
<?php include 'footer.php'; ?>
