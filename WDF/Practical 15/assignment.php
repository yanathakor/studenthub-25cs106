<?php
require_once 'config.php';
require_login();

if (is_admin() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (isset($_POST['add'])) {
        $course = (int)$_POST['course_id']; $t = trim($_POST['title']);
        $d = trim($_POST['description']); $due = $_POST['due_date'];
        $orig = null; $stored = null; $ok = true;

        if (!empty($_FILES['file']['name'])) {
            $allowed = ['pdf','doc','docx','txt','zip','ppt','pptx'];
            $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($_FILES['file']['error'] !== UPLOAD_ERR_OK || $_FILES['file']['size'] > 5 * 1024 * 1024 || !in_array($ext, $allowed)) {
                flash("Invalid file (allowed: " . implode(', ', $allowed) . "; max 5MB).", "error");
                $ok = false;
            } else {
                $orig = basename($_FILES['file']['name']);
                $stored = bin2hex(random_bytes(16)) . '.' . $ext;
                move_uploaded_file($_FILES['file']['tmp_name'], UPLOAD_DIR . $stored);
            }
        }
        if ($ok && $t && $due && $course) {
            $st = mysqli_prepare($conn, "INSERT INTO assignments (course_id,title,description,due_date,file_name,stored_name) VALUES (?,?,?,?,?,?)");
            mysqli_stmt_bind_param($st, "isssss", $course, $t, $d, $due, $orig, $stored);
            mysqli_stmt_execute($st);
            flash("Assignment added.");
        } elseif ($ok) flash("Fill all required fields.", "error");
    } elseif (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $g = mysqli_prepare($conn, "SELECT stored_name FROM assignments WHERE id=?");
        mysqli_stmt_bind_param($g, "i", $id);
        mysqli_stmt_execute($g);
        $r = mysqli_fetch_assoc(mysqli_stmt_get_result($g));
        if ($r && $r['stored_name'] && is_file(UPLOAD_DIR . basename($r['stored_name']))) unlink(UPLOAD_DIR . basename($r['stored_name']));
        $st = mysqli_prepare($conn, "DELETE FROM assignments WHERE id=?");
        mysqli_stmt_bind_param($st, "i", $id);
        mysqli_stmt_execute($st);
        flash("Assignment deleted.");
    }
    header("Location: assignment.php"); exit;
}

$list = mysqli_query($conn, "SELECT a.*, c.code FROM assignments a JOIN courses c ON c.id=a.course_id ORDER BY a.due_date");
$courses = mysqli_query($conn, "SELECT id,code,name FROM courses ORDER BY code");
$title = "Assignments";
include 'sidebar.php';
?>
<table class="table">
  <tr><th>Course</th><th>Title</th><th>Description</th><th>Due</th><th>File</th><?php if (is_admin()) echo '<th>Action</th>'; ?></tr>
  <?php while ($a = mysqli_fetch_assoc($list)): ?>
  <tr>
    <td><?= e($a['code']) ?></td><td><?= e($a['title']) ?></td><td><?= e($a['description']) ?></td>
    <td><?= e($a['due_date']) ?></td>
    <td><?php if ($a['stored_name']): ?><a href="assignment_download.php?id=<?= (int)$a['id'] ?>">⬇ <?= e($a['file_name']) ?></a><?php else: ?>—<?php endif; ?></td>
    <?php if (is_admin()): ?>
    <td>
      <form method="post" class="inline" onsubmit="return confirm('Delete this assignment?')">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
        <button class="btn small danger" name="delete">Delete</button>
      </form>
    </td>
    <?php endif; ?>
  </tr>
  <?php endwhile; ?>
</table>

<?php if (is_admin()): ?>
<div class="panel">
  <h3>Add assignment</h3>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <label>Course</label>
    <select name="course_id" required>
      <?php while ($c = mysqli_fetch_assoc($courses)) echo '<option value="' . (int)$c['id'] . '">' . e($c['code'] . ' - ' . $c['name']) . '</option>'; ?>
    </select>
    <label>Title</label><input name="title" required>
    <label>Description</label><textarea name="description" rows="3"></textarea>
    <label>Due date</label><input type="date" name="due_date" required>
    <label>Attachment (optional)</label><input type="file" name="file">
    <button class="btn" name="add">Add assignment</button>
  </form>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
