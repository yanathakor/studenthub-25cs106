<?php
require_once 'config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $n = trim($_POST['name'] ?? ''); $em = trim($_POST['email'] ?? ''); $m = trim($_POST['message'] ?? '');
    if ($n && filter_var($em, FILTER_VALIDATE_EMAIL) && strlen($m) >= 10) {
        $st = mysqli_prepare($conn, "INSERT INTO contacts (name,email,message) VALUES (?,?,?)");
        mysqli_stmt_bind_param($st, "sss", $n, $em, $m);
        mysqli_stmt_execute($st);
        flash("Thanks! Your message has been sent.");
    } else flash("Fill all fields (message at least 10 characters).", "error");
    header("Location: contact.php"); exit;
}
$title = "Contact Us";
include 'sidebar.php';
?>
<div class="panel narrow">
  <form method="post">
    <?= csrf_field() ?>
    <label>Name</label><input name="name" value="<?= e($_SESSION['name']) ?>" required>
    <label>Email</label><input type="email" name="email" required>
    <label>Message</label><textarea name="message" rows="5" required></textarea>
    <button class="btn">Send message</button>
  </form>
</div>
<?php include 'footer.php'; ?>
