
<?php
$links = [
    'index'      => ['index.php', 'Home'],
    'profile'    => ['profile.php', 'Profile'],
    'courses'    => ['courses.php', 'Courses'],
    'assignment' => ['assignment.php', 'Assignment'],
    'attendance' => ['attendance.php', 'Attendance'],
    'result'     => ['result.php', 'Results'],
    'contact'    => ['contact.php', 'Contact']
];

if (currentRole() === 'admin') {
    $links['admin'] = ['admin.php', 'Admin Dashboard'];
}
?>
<div class="sidebar">
    <h2>StudentHub</h2>

    <ul>
        <?php foreach ($links as $key => $link): ?>
            <li<?= (($activePage ?? '') === $key) ? ' class="active"' : '' ?>>
                <a href="<?= e($link[0]) ?>"><?= e($link[1]) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <form method="POST" action="logout.php" style="margin-top:25px;padding:0 20px;">
        <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
        <p style="color:#fff;font-size:14px;">
            <?= e($_SESSION['user_name'] ?? '') ?>
            (<?= e(ucfirst($_SESSION['role'] ?? '')) ?>)
        </p>
        <button type="submit">Logout</button>
    </form>
</div>