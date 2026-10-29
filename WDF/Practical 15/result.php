<?php
require_once 'config.php';
require_login();

function grade($m) {
    if ($m >= 90) return 'A+'; if ($m >= 80) return 'A'; if ($m >= 70) return 'B';
    if ($m >= 60) return 'C'; if ($m >= 40) return 'D'; return 'F';
}

if (is_admin() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $u = (int)$_POST['user_id']; $c = (int)$_POST['course_id']; $m = (float)$_POST['marks'];
    if ($u && $c && $m >= 0 && $m <= 100) {
        $st = mysqli_prepare($conn, "INSERT INTO results (user_id,course_id,marks) VALUES (?,?,?)");
        mysqli_stmt_bind_param($st, "iid", $u, $c, $m);
        mysqli_stmt_execute($st);
        flash("Result saved.");
    } else flash("Marks must be between 0 and 100.", "error");
    header("Location: result.php"); exit;
}

if (is_admin()) {
    $rows = mysqli_query($conn, "SELECT r.*, u.name, c.code, c.name AS cname FROM results r JOIN users u ON u.id=r.user_id JOIN courses c ON c.id=r.course_id ORDER BY u.name, c.code");
    $students = mysqli_query($conn, "SELECT id,name FROM users WHERE role='student' ORDER BY name");
    $courses  = mysqli_query($conn, "SELECT id,code,name FROM courses ORDER BY code");
} else {
    $st = mysqli_prepare($conn, "SELECT r.*, c.code, c.name AS cname FROM results r JOIN courses c ON c.id=r.course_id WHERE r.user_id=? ORDER BY c.code");
    mysqli_stmt_bind_param($st, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($st);
    $rows = mysqli_stmt_get_result($st);
}
$title = "Results";
include 'sidebar.php';
?>
<table class="table">
  <tr><?php if (is_admin()) echo '<th>Student</th>'; ?><th>Course</th><th>Marks</th><th>Grade</th></tr>
  <?php while ($r = mysqli_fetch_assoc($rows)): ?>
  <tr>
    <?php if (is_admin()) echo '<td>' . e($r['name']) . '</td>'; ?>
    <td><?= e($r['code'] . ' - ' . $r['cname']) ?></td>
    <td><?= e($r['marks']) ?></td>
    <td><span class="tag <?= $r['marks'] >= 40 ? 'ok' : 'bad' ?>"><?= grade($r['marks']) ?></span></td>
  </tr>
  <?php endwhile; ?>
</table>

<?php if (is_admin()): ?>
<div class="panel">
  <h3>Add result</h3>
  <form method="post" class="row">
    <?= csrf_field() ?>
    <select name="user_id" required><?php while ($s = mysqli_fetch_assoc($students)) echo '<option value="' . (int)$s['id'] . '">' . e($s['name']) . '</option>'; ?></select>
    <select name="course_id" required><?php while ($c = mysqli_fetch_assoc($courses)) echo '<option value="' . (int)$c['id'] . '">' . e($c['code']) . '</option>'; ?></select>
    <input type="number" name="marks" step="0.01" min="0" max="100" placeholder="Marks" required>
    <button class="btn">Save</button>
  </form>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
