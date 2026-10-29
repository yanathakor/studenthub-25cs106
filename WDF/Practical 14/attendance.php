<?php
require 'config.php';
requireLogin();
$activePage = 'attendance';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>StudentHub - Attendance</title>

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

                <input type="text" placeholder="Search">

            </div>

            <a href="profile.php" class="profile">
                <i class="fas fa-user"></i>
            </a>

        </div>

        <h1>Attendance</h1>

        <div class="attendance-container">

            <div class="attendance-circle">

                <div class="circle">
                    <h2>92%</h2>
                    <p>Overall</p>
                </div>

            </div>

            <div class="attendance-table">

                <table>

                    <tr>
                        <th>Subject</th>
                        <th>Attendance</th>
                    </tr>

                    <tr>
                        <td>Web Development</td>
                        <td>95%</td>
                    </tr>

                    <tr>
                        <td>Java Programming</td>
                        <td>90%</td>
                    </tr>

                    <tr>
                        <td>Database</td>
                        <td>93%</td>
                    </tr>

                    <tr>
                        <td>Computer Network</td>
                        <td>88%</td>
                    </tr>

                    <tr>
                        <td>Digital Electronics</td>
                        <td>94%</td>
                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html><?php
require 'config.php';
requireLogin();
$activePage = 'attendance';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>StudentHub - Attendance</title>

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

                <input type="text" placeholder="Search">

            </div>

            <a href="profile.php" class="profile">
                <i class="fas fa-user"></i>
            </a>

        </div>

        <h1>Attendance</h1>

        <div class="attendance-container">

            <div class="attendance-circle">

                <div class="circle">
                    <h2>92%</h2>
                    <p>Overall</p>
                </div>

            </div>

            <div class="attendance-table">

                <table>

                    <tr>
                        <th>Subject</th>
                        <th>Attendance</th>
                    </tr>

                    <tr>
                        <td>Web Development</td>
                        <td>95%</td>
                    </tr>

                    <tr>
                        <td>Java Programming</td>
                        <td>90%</td>
                    </tr>

                    <tr>
                        <td>Database</td>
                        <td>93%</td>
                    </tr>

                    <tr>
                        <td>Computer Network</td>
                        <td>88%</td>
                    </tr>

                    <tr>
                        <td>Digital Electronics</td>
                        <td>94%</td>
                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>