<?php
require_once 'config.php';
require_login();

function scalar($conn, $sql, $types = "", $params = []) {
    $stmt = mysqli_prepare($conn, $sql);
    if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_row(mysqli_stmt_get_result($stmt));
    return $row[0] ?? 0;
}

$uid = $_SESSION['user_id'];
if (is_admin()) {
    $cards = [
        ['Students', scalar($conn, "SELECT COUNT(*) FROM users WHERE role='student'")],
        ['Courses', scalar($conn, "SELECT COUNT(*) FROM courses")],
        ['Assignments', scalar($conn, "SELECT COUNT(*) FROM assignments")],
        ['Messages', scalar($conn, "SELECT COUNT(*) FROM contacts")],
    ];
} else {
    $total   = scalar($conn, "SELECT COUNT(*) FROM attendance WHERE user_id=?", "i", [$uid]);
    $present = scalar($conn, "SELECT COUNT(*) FROM attendance WHERE user_id=? AND status='Present'", "i", [$uid]);
    $avg     = scalar($conn, "SELECT ROUND(AVG(marks),1) FROM results WHERE user_id=?", "i", [$uid]);
    $cards = [
        ['Courses', scalar($conn, "SELECT COUNT(*) FROM courses")],
        ['Assignments', scalar($conn, "SELECT COUNT(*) FROM assignments")],
        ['Attendance', $total ? round($present / $total * 100) . '%' : 'N/A'],
        ['Average Marks', $avg ?: 'N/A'],
    ];
}
$title = "Dashboard";
include 'sidebar.php';
?>
<p>Welcome back, <strong><?= e($_SESSION['name']) ?></strong>!</p>
<div class="cards">
  <?php foreach ($cards as $c): ?>
    <div class="card"><div class="num"><?= e($c[1]) ?></div><div><?= e($c[0]) ?></div></div>
  <?php endforeach; ?>
</div>
<?php include 'footer.php'; ?>
