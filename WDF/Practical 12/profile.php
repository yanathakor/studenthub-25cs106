<?php
require 'config.php';
requireLogin();
$activePage = 'profile';

$uid = (int)$_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email, mobile, gender, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$u) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Profile</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

</head>


<body>

<div id="modal" class="modal">

    <div class="modal-content">

        <button class="close" onclick="closeModal()">
            &times;
        </button>

        <h2>Student Information</h2>

        <p>
            <strong>Name:</strong>
            <?= e($u['full_name']) ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?= e($u['email']) ?>
        </p>

        <p>
            <strong>Role:</strong>
            <?= e(ucfirst($u['role'])) ?>
        </p>

    </div>

</div>


<div class="container">

    <?php include 'sidebar.php'; ?>

    <div class="main">

        <div class="header">

            <div class="search">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    placeholder="Search">

            </div>

            <button
                class="profile-icon"
                onclick="openModal()"
                aria-label="Open student information">

                <i class="fa-solid fa-user"></i>

            </button>

        </div>

        <div class="profile-card">

            <div class="top">

                <div class="profile">

                    <i class="fas fa-user"></i>

                </div>

                <div class="details">

                    <h2>
                        <?= e($u['full_name']) ?>
                    </h2>

                    <p>
                        <?= e(ucfirst($u['role'])) ?>
                    </p>

                    <p>
                        CHARUSAT University
                    </p>

                </div>

                <div class="edit">

                    <button>
                        Edit Profile
                    </button>

                </div>

            </div>

            <table>

                <tr>
                    <th>Email</th>
                    <td><?= e($u['email']) ?></td>
                </tr>

                <tr>
                    <th>Phone</th>
                    <td><?= e($u['mobile']) ?></td>
                </tr>

                <tr>
                    <th>Gender</th>
                    <td><?= e($u['gender']) ?></td>
                </tr>

                <tr>
                    <th>Role</th>
                    <td><?= e(ucfirst($u['role'])) ?></td>
                </tr>

                <tr>
                    <th>Member Since</th>
                    <td><?= e(date('d M Y', strtotime($u['created_at']))) ?></td>
                </tr>

            </table>

        </div>

    </div>

</div>

<script>

    function openModal() {
        document.getElementById("modal").classList.add("show");
    }

    function closeModal() {
        document.getElementById("modal").classList.remove("show");
    }

    document.getElementById("modal").addEventListener("click", function (event) {
        if (event.target === this) {
            closeModal();
        }
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeModal();
        }
    });

</script>

</body>

</html>