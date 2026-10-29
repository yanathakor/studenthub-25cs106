<?php
require_once 'config.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_msg'])) {
    csrf_check();
    $id = (int)$_POST['id'];
    $st = mysqli_prepare($conn, "DELETE FROM contacts WHERE id=?");
    mysqli_stmt_bind_param($st, "i", $id);
    mysqli_stmt_execute($st);
    flash("Message deleted.");
    header("Location: admin.php"); exit;
}

$stats = [
    'Students'    => mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='student'"))[0],
    'Admins'      => mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='admin'"))[0],
    'Courses'     => mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM courses"))[0],
    'Assignments' => mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM assignments"))[0],
];
$msgs = mysqli_query($conn, "SELECT * FROM contacts ORDER BY created_at DESC");
$title = "Admin Panel";
include 'sidebar.php';
?>
<div class="cards">
  <?php foreach ($stats as $k => $v): ?>
    <div class="card"><div class="num"><?= (int)$v ?></div><div><?= e($k) ?></div></div>
  <?php endforeach; ?>
</div>

<h3>Contact messages</h3>
<table class="table">
  <tr><th>Name</th><th>Email</th><th>Message</th><th>Date</th><th></th></tr>
  <?php while ($m = mysqli_fetch_assoc($msgs)): ?>
  <tr>
    <td><?= e($m['name']) ?></td><td><?= e($m['email']) ?></td><td><?= nl2br(e($m['message'])) ?></td><td><?= e($m['created_at']) ?></td>
    <td>
      <form method="post" class="inline" onsubmit="return confirm('Delete message?')">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
        <button class="btn small danger" name="delete_msg">Delete</button>
      </form>
    </td>
  </tr>
  <?php endwhile; ?>
</table>
<?php include 'footer.php'; ?>
