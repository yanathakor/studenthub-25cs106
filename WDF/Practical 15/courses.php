<?php
require_once 'config.php';
require_login();

if (is_admin() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (isset($_POST['add'])) {
        $code = strtoupper(trim($_POST['code'])); $name = trim($_POST['name']);
        $cred = (int)$_POST['credits']; $ins = trim($_POST['instructor']);
        if ($code && $name && $ins && $cred > 0) {
            $st = mysqli_prepare($conn, "INSERT INTO courses (code,name,credits,instructor) VALUES (?,?,?,?)");
            mysqli_stmt_bind_param($st, "ssis", $code, $name, $cred, $ins);
            if (mysqli_stmt_execute($st)) flash("Course added."); else flash("Course code already exists.", "error");
        } else flash("Fill all fields.", "error");
    } elseif (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $st = mysqli_prepare($conn, "DELETE FROM courses WHERE id=?");
        mysqli_stmt_bind_param($st, "i", $id);
        mysqli_stmt_execute($st);
        flash("Course deleted.");
    }
    header("Location: courses.php"); exit;
}

$res = mysqli_query($conn, "SELECT * FROM courses ORDER BY code");
$title = "Courses";
include 'sidebar.php';
?>
<input type="text" id="search" class="search" placeholder="Search courses...">
<table class="table" id="courseTable">
  <tr><th>Code</th><th>Course</th><th>Credits</th><th>Instructor</th><?php if (is_admin()) echo '<th>Action</th>'; ?></tr>
  <?php while ($c = mysqli_fetch_assoc($res)): ?>
  <tr>
    <td><?= e($c['code']) ?></td><td><?= e($c['name']) ?></td>
    <td><?= (int)$c['credits'] ?></td><td><?= e($c['instructor']) ?></td>
    <?php if (is_admin()): ?>
    <td>
      <form method="post" class="inline" onsubmit="return confirm('Delete this course?')">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
        <button class="btn small danger" name="delete">Delete</button>
      </form>
    </td>
    <?php endif; ?>
  </tr>
  <?php endwhile; ?>
</table>

<?php if (is_admin()): ?>
<div class="panel">
  <h3>Add course</h3>
  <form method="post" class="row">
    <?= csrf_field() ?>
    <input name="code" placeholder="Code (CE105)" required>
    <input name="name" placeholder="Course name" required>
    <input name="credits" type="number" min="1" max="10" placeholder="Credits" required>
    <input name="instructor" placeholder="Instructor" required>
    <button class="btn" name="add">Add</button>
  </form>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
