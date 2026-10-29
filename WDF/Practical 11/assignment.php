<?php
require 'config.php';
requireLogin();
$activePage = 'assignment';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Assignment</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
</head>

<body>

    <div class="container">

        <?php include 'sidebar.php'; ?>

        <div class="main">

            <div class="header">

                <div class="search">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input
                        type="text"
                        id="searchAssignment"
                        placeholder="Search Assignment">

                </div>

                <a href="profile.php" class="profile">
                    <i class="fas fa-user"></i>
                </a>

            </div>

            <h1>Assignments</h1>

            <div class="assignment-box" id="assignmentBox">

            </div>

        </div>

    </div>

    <script src="assignment.js"></script>

</body>

</html>