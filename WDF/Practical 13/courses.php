
<?php
require 'config.php';
requireLogin();
$activePage = 'courses';
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses | StudentHub</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .course-actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:15px; }
        .course-actions button, .course-form button {
            padding:9px 14px; border:0; border-radius:6px; cursor:pointer;
        }
        .course-actions button { background:#55828b; color:white; }
        .course-actions .delete-btn { background:#b42318; }
        .course-form {
            margin:20px 0; padding:20px; background:#f4f8f7;
            border-radius:12px; display:grid; gap:12px;
        }
        .course-form input, .course-form textarea {
            width:100%; padding:11px; border:1px solid #ccc;
            border-radius:6px; box-sizing:border-box;
        }
        .course-form button { background:#3b6064; color:white; }
        .course-message { margin:12px 0; }
        .course-card { padding:18px; }
        .course-card > i { font-size:25px; }
        [hidden] { display:none !important; }
    </style>
</head>
<body>
<div class="container">
    <?php include 'sidebar.php'; ?>

    <div class="main">
        <div class="header">
            <div class="search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search Courses" id="searchCourse">
            </div>
            <a href="profile.php" class="profile">
                <i class="fas fa-user"></i>
            </a>
        </div>

        <h1 class="title">My Courses</h1>
        <p id="courseMessage" class="course-message" role="status"></p>

        <?php if ($isAdmin): ?>
        <form id="courseForm" class="course-form">
            <h2 id="formHeading">Add New Course</h2>
            <input type="hidden" id="courseId">

            <label for="courseName">Course Name</label>
            <input id="courseName" maxlength="150" required>

            <label for="courseDescription">Description</label>
            <textarea id="courseDescription" maxlength="500" rows="3" required></textarea>

            <label for="courseIcon">Font Awesome Icon Class</label>
            <input id="courseIcon" maxlength="100" value="fas fa-book">

            <div class="course-actions">
                <button type="submit">Save Course</button>
                <button type="button" id="cancelEdit" hidden>Cancel Edit</button>
            </div>
        </form>
        <?php endif; ?>

        <div class="course-container" id="courseContainer"></div>
    </div>
</div>

<script>
    window.courseAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
</script>
<script src="course.js"></script>
</body>
</html>