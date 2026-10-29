<?php
require_once 'config.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (isset($_POST['add'])) {
        $n = trim($_POST['name']); $em = trim($_POST['email']); $p = $_POST['password'];
        if (strlen($n) < 3 || !filter_var($em, FILTER_VALIDATE_EMAIL) || strlen($p) < 8) {
            flash("Enter valid name, email and a password of 8+ characters.", "error");
        } else {
            $h = password_hash($p, PASSWORD_DEFAULT);
            $st = mysqli_prepare($conn, "INSERT INTO users (name,email,password,role) VALUES (?,?,?,'student')");
            mysqli_stmt_bind_param($st, "sss", $n, $em, $h);
            if (mysqli_stmt_execute($st)) flash("Student added."); else flash("Email already exists.", "error");
        }
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id']; $n = trim($_POST['name']); $em = trim($_POST['email']);
        if (strlen($n) < 3 || !filter_var($em, FILTER_VALIDATE_EMAIL)) {
            flash("Invalid name or email.", "error");
        } else {
            $st = mysqli_prepare($conn, "UPDATE users SET name=?, email=? WHERE id=? AND role='student'");
            mysqli_stmt_bind_param($st, "ssi", $n, $em, $id);
            if (mysqli_stmt_execute($st)) flash("Student updated."); else flash("Email already exists.", "error");
        }
    } elseif (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $st = mysqli_prepare($conn, "DELETE FROM users WHERE id=? AND role='student'");
        mysqli_stmt_bind_param($st, "i", $id);
        mysqli_stmt_execute($st);
        flash("Student deleted.");
    }
    header("Location: student_management.php"); exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $st = mysqli_prepare($conn, "SELECT id,name,email FROM users WHERE id=? AND role='student'");
    mysqli_stmt_bind_param($st, "i", $id);
    mysqli_stmt_execute($st);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
}
$rows = mysqli_query($conn, "SELECT id,name,email,created_at FROM users WHERE role='student' ORDER BY name");
$title = "Student Management";
include 'sidebar.php';
?>
<table class="table">
  <tr><th>#</th><th>Name</th><th>Email</th><th>Joined</th><th>Actions</th></tr>
  <?php while ($s = mysqli_fetch_assoc($rows)): ?>
  <tr>
    <td><?= (int)$s['id'] ?></td><td><?= e($s['name']) ?></td><td><?= e($s['email']) ?></td><td><?= e($s['created_at']) ?></td>
    <td>
      <a class="btn small" href="?edit=<?= (int)$s['id'] ?>">Edit</a>
      <form method="post" class="inline" onsubmit="return confirm('Delete this student and all their records?')">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
        <button class="btn small danger" name="delete">Delete</button>
      </form>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

<div class="panel">
  <?php if ($edit): ?>
    <h3>Edit student</h3>
    <form method="post" class="row">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
      <input name="name" value="<?= e($edit['name']) ?>" required>
      <input type="email" name="email" value="<?= e($edit['email']) ?>" required>
      <button class="btn" name="update">Update</button>
      <a class="btn small gray" href="student_management.php">Cancel</a>
    </form>
  <?php else: ?>
    <h3>Add student</h3>
    <form method="post" class="row">
      <?= csrf_field() ?>
      <input name="name" placeholder="Full name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password (8+)" required>
      <button class="btn" name="add">Add</button>
    </form>
  <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
