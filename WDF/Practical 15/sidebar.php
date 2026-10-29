<?php
// Layout start: <head>, sidebar and opening <main>. Include AFTER require_login() and any redirects.
$current = basename($_SERVER['PHP_SELF']);
$title   = $title ?? 'StudentHub';
function nav_link($file, $label, $current) {
    $cls = ($current === $file) ? 'active' : '';
    echo '<a class="' . $cls . '" href="' . $file . '">' . $label . '</a>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> - StudentHub</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<button class="menu-btn" id="menuBtn">☰</button>
<aside class="sidebar" id="sidebar">
  <h2>🎓 StudentHub</h2>
  <div class="user-box">
    <strong><?= e($_SESSION['name']) ?></strong>
    <span class="badge"><?= e(ucfirst($_SESSION['role'])) ?></span>
  </div>
  <nav>
    <?php
    nav_link('index.php', '🏠 Dashboard', $current);
    nav_link('profile.php', '👤 Profile', $current);
    nav_link('courses.php', '📚 Courses', $current);
    nav_link('assignment.php', '📝 Assignments', $current);
    nav_link('attendance.php', '🗓 Attendance', $current);
    nav_link('result.php', '📊 Results', $current);
    if (is_admin()) {
        nav_link('student_management.php', '🧑‍🎓 Students', $current);
        nav_link('admin.php', '⚙ Admin Panel', $current);
    }
    nav_link('contact.php', '✉ Contact', $current);
    ?>
    <a href="logout.php" class="logout">🚪 Logout</a>
  </nav>
</aside>
<main class="content">
<h1 class="page-title"><?= e($title) ?></h1>
<?php show_flash(); ?>
