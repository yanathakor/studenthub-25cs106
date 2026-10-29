<?php
require 'config.php';
requireLogin();
$activePage = 'result';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>StudentHub - Results</title>

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

                <i class="fa-solid fa-user"></i>

            </a>

        </div>

        <h1>Semester Results</h1>

        <div class="result-card">

            <table>

                <tr>
                    <th>Subject</th>
                    <th>Marks</th>
                    <th>Grade</th>
                </tr>

                <tr>
                    <td>Web Development</td>
                    <td>92</td>
                    <td>A+</td>
                </tr>

                <tr>
                    <td>Java Programming</td>
                    <td>88</td>
                    <td>A</td>
                </tr>

                <tr>
                    <td>Database</td>
                    <td>90</td>
                    <td>A+</td>
                </tr>

                <tr>
                    <td>Computer Network</td>
                    <td>85</td>
                    <td>A</td>
                </tr>

                <tr>
                    <td>Digital Electronics</td>
                    <td>91</td>
                    <td>A+</td>
                </tr>

                <tr class="cgpa">
                    <td colspan="2"><strong>CGPA</strong></td>
                    <td><strong>8.75</strong></td>
                </tr>

            </table>

        </div>

    </div>

</div>

</body>
</html>