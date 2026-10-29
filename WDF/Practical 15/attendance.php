<?php
require_once 'config.php';
require_login();

if (is_admin() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $u = (int)$_POST['user_id']; $c = (int)$_POST['course_id'];
    $d = $_POST['att_date']; $s = $_POST['status'] === 'Absent' ? 'Absent' : 'Present';
    if ($u && $c && $d) {
        $st = mysqli_prepare($conn, "INSERT INTO attendance (user_id,course_id,att_date,status) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($st, "iiss", $u, $c, $d, $s);
        mysqli_stmt_execute($st);
        flash("Attendance recorded.");
    } else flash("Fill all fields.", "error");
    header("Location: attendance.php"); exit;
}

if (is_admin()) {
    $rows = mysqli_query($conn, "SELECT a.*, u.name, c.code FROM attendance a JOIN users u ON u.id=a.user_id JOIN courses c ON c.id=a.course_id ORDER BY a.att_date DESC, u.name LIMIT 200");
    $students = mysqli_query($conn, "SELECT id,name FROM users WHERE role='student' ORDER BY name");
    $courses  = mysqli_query($conn, "SELECT id,code,name FROM courses ORDER BY code");
} else {
    $st = mysqli_prepare($conn, "SELECT a.*, c.code FROM attendance a JOIN courses c ON c.id=a.course_id WHERE a.user_id=? ORDER BY a.att_date DESC");
    mysqli_stmt_bind_param($st, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($st);
    $rows = mysqli_stmt_get_result($st);

    $sm = mysqli_prepare($conn, "SELECT c.code, c.name, COUNT(*) total, SUM(a.status='Present') present FROM attendance a JOIN courses c ON c.id=a.course_id WHERE a.user_id=? GROUP BY c.id");
    mysqli_stmt_bind_param($sm, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($sm);
    $summary = mysqli_fetch_all(mysqli_stmt_get_result($sm), MYSQLI_ASSOC);
}
$title = "Attendance";
include 'sidebar.php';
?>
<?php if (!is_admin() && $summary): ?>
<div class="cards">
  <?php foreach ($summary as $s): $p = round($s['present'] / $s['total'] * 100); ?>
    <div class="card"><div class="num <?= $p < 75 ? 'low' : '' ?>"><?= $p ?>%</div><div><?= e($s['code']) ?></div></div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<table class="table">
  <tr><?php if (is_admin()) echo '<th>Student</th>'; ?><th>Course</th><th>Date</th><th>Status</th></tr>
  <?php while ($r = mysqli_fetch_assoc($rows)): ?>
  <tr>
    <?php if (is_admin()) echo '<td>' . e($r['name']) . '</td>'; ?>
    <td><?= e($r['code']) ?></td><td><?= e($r['att_date']) ?></td>
    <td><span class="tag <?= $r['status'] === 'Present' ? 'ok' : 'bad' ?>"><?= e($r['status']) ?></span></td>
  </tr>
  <?php endwhile; ?>
</table>

<?php if (is_admin()): ?>
<div class="panel">
  <h3>Mark attendance</h3>
  <form method="post" class="row">
    <?= csrf_field() ?>
    <select name="user_id" required><?php while ($s = mysqli_fetch_assoc($students)) echo '<option value="' . (int)$s['id'] . '">' . e($s['name']) . '</option>'; ?></select>
    <select name="course_id" required><?php while ($c = mysqli_fetch_assoc($courses)) echo '<option value="' . (int)$c['id'] . '">' . e($c['code']) . '</option>'; ?></select>
    <input type="date" name="att_date" value="<?= date('Y-m-d') ?>" required>
    <select name="status"><option>Present</option><option>Absent</option></select>
    <button class="btn">Save</button>
  </form>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
