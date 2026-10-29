<?php
require 'config.php';
requireLogin();
$activePage = 'courses';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Courses | StudentHub</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<div class="container">

    <?php include 'sidebar.php'; ?>

    <div class="main">

        <div class="header">

            <div class="search">

                <i class="fas fa-search"></i>

                <input
                    type="text"
                    placeholder="Search Courses"
                    id="searchCourse">

            </div>

            <a href="profile.php" class="profile">

                <i class="fas fa-user"></i>

            </a>

        </div>

        <h1 class="title">My Courses</h1>

        <div class="course-container" id="courseContainer">

        </div>

    </div>

</div>

<script src="course.js"></script>

</body>

</html>