<?php
require 'config.php';
requireLogin();

$activePage = 'assignment';

$userId = (int)($_SESSION['user_id'] ?? 0);
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

$message = '';
$uploadDir = __DIR__ . '/assignment_uploads/';

if ($userId <= 0) {
    http_response_code(403);
    exit('Please log in again.');
}

/* Student assignment upload */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isAdmin) {
        $message = 'Only students can upload assignments.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $file = $_FILES['assignment_file'] ?? null;

        if ($title === '' || strlen($title) > 150 || !$file) {
            $message = 'Please enter the assignment title and select a file.';
        } elseif ($file['error'] !== UPLOAD_ERR_OK) {
            $message = 'File upload failed. Please select a valid file.';
        } elseif ($file['size'] <= 0 || $file['size'] > 5 * 1024 * 1024) {
            $message = 'File size must be greater than 0 and at most 5 MB.';
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

            $allowedTypes = [
                'application/pdf' => 'pdf',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
                'application/zip' => 'zip',
                'application/x-zip-compressed' => 'zip'
            ];

            if (!isset($allowedTypes[$mime])) {
                $message = 'Only PDF, DOC, DOCX, PPTX, and ZIP files are allowed.';
            } else {
                if (!is_dir($uploadDir) &&
                    !mkdir($uploadDir, 0755, true) &&
                    !is_dir($uploadDir)) {
                    $message = 'Upload folder could not be created.';
                } else {
                    $storedName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];
                    $filePath = $uploadDir . $storedName;

                    if (move_uploaded_file($file['tmp_name'], $filePath)) {
                        $stmt = $conn->prepare(
                            'INSERT INTO assignment_submissions
                            (student_id, title, description, file_name)
                            VALUES (?, ?, ?, ?)'
                        );

                        $stmt->bind_param(
                            'isss',
                            $userId,
                            $title,
                            $description,
                            $storedName
                        );

                        if ($stmt->execute()) {
                            $message = 'Assignment uploaded successfully!';
                        } else {
                            unlink($filePath);
                            $message = 'Could not save the assignment in the database.';
                        }

                        $stmt->close();
                    } else {
                        $message = 'Could not save the uploaded file.';
                    }
                }
            }
        }
    }
}

/* Fetch assignments for the current role */
if ($isAdmin) {
    $stmt = $conn->prepare(
        'SELECT s.id, s.student_id, s.title, s.description,
                s.file_name, s.submitted_at, s.status,
                u.full_name, u.email
         FROM assignment_submissions s
         INNER JOIN users u ON u.id = s.student_id
         ORDER BY s.submitted_at DESC'
    );
} else {
    $stmt = $conn->prepare(
        'SELECT id, title, description, file_name,
                submitted_at, status
         FROM assignment_submissions
         WHERE student_id = ?
         ORDER BY submitted_at DESC'
    );

    $stmt->bind_param('i', $userId);
}

$stmt->execute();
$submissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Assignments</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

    <style>
        .submission-section {
            margin-top: 30px;
            padding: 22px;
            background: #f8fbfa;
            border-radius: 12px;
        }

        .submission-section h2 {
            color: #364958;
            margin-bottom: 18px;
        }

        .submission-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 650px;
        }

        .submission-form label {
            font-weight: 600;
            color: #364958;
        }

        .submission-form input,
        .submission-form textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #c9d5d5;
            border-radius: 6px;
            box-sizing: border-box;
            font: inherit;
        }

        .submission-form button {
            padding: 12px 18px;
            border: none;
            border-radius: 6px;
            background: #3b6064;
            color: white;
            cursor: pointer;
            font-size: 15px;
            margin-top: 8px;
        }

        .submission-form button:hover {
            background: #364958;
        }

        .status-message {
            padding: 12px;
            margin-bottom: 18px;
            background: #e5f3e8;
            border-radius: 6px;
            overflow-wrap: anywhere;
        }

        .submission-card {
            background: white;
            border: 1px solid #dce6e3;
            border-radius: 9px;
            padding: 18px;
            margin: 15px 0;
            overflow-wrap: anywhere;
        }

        .submission-card h3 {
            color: #364958;
            margin-top: 0;
        }

        .submission-card a {
            display: inline-block;
            margin-top: 8px;
            color: #3b6064;
            font-weight: 600;
        }

        .submission-card p {
            margin: 8px 0;
        }

        @media (max-width: 600px) {
            .submission-section {
                padding: 14px;
            }
        }
    </style>
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
                    placeholder="Search Assignment"
                    aria-label="Search assignments"
                >
            </div>

            <a href="profile.php" class="profile" aria-label="Profile">
                <i class="fas fa-user"></i>
            </a>
        </div>

        <h1>Assignments</h1>

        <!-- Existing assignment list -->
        <div class="assignment-box" id="assignmentBox"></div>

        <!-- Upload and submission management on the same page -->
        <section class="submission-section">

            <h2>
                <?= $isAdmin
                    ? 'Student Assignment Submissions'
                    : 'Upload Your Assignment' ?>
            </h2>

            <?php if ($message !== ''): ?>
                <p class="status-message" role="status">
                    <?= e($message) ?>
                </p>
            <?php endif; ?>

            <?php if (!$isAdmin): ?>
                <form
                    class="submission-form"
                    method="POST"
                    action="assignment.php"
                    enctype="multipart/form-data"
                >
                    <label for="title">Assignment Title</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        maxlength="150"
                        required
                    >

                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Enter assignment details"
                    ></textarea>

                    <label for="assignment_file">
                        Choose File (PDF, DOC, DOCX, PPTX, ZIP; maximum 5 MB)
                    </label>
                    <input
                        type="file"
                        id="assignment_file"
                        name="assignment_file"
                        accept=".pdf,.doc,.docx,.pptx,.zip"
                        required
                    >

                    <button type="submit">
                        <i class="fa-solid fa-upload"></i>
                        Upload Assignment
                    </button>
                </form>
            <?php endif; ?>

            <h2 style="margin-top: 28px;">
                <?= $isAdmin ? 'All Student Submissions' : 'My Submissions' ?>
            </h2>

            <?php if (empty($submissions)): ?>
                <p>No assignments submitted yet.</p>
            <?php else: ?>

                <?php foreach ($submissions as $submission): ?>
                    <article class="submission-card">

                        <h3><?= e($submission['title']) ?></h3>

                        <?php if ($isAdmin): ?>
                            <p>
                                <strong>Student:</strong>
                                <?= e($submission['full_name']) ?>
                            </p>
                            <p>
                                <strong>Email:</strong>
                                <?= e($submission['email']) ?>
                            </p>
                            <p>
                                <strong>Student ID:</strong>
                                <?= (int)$submission['student_id'] ?>
                            </p>
                        <?php endif; ?>

                        <p>
                            <strong>Description:</strong>
                            <?= nl2br(e($submission['description'] ?? '')) ?>
                        </p>

                        <p>
                            <strong>Submitted:</strong>
                            <?= e($submission['submitted_at']) ?>
                        </p>

                        <p>
                            <strong>Status:</strong>
                            <?= e($submission['status']) ?>
                        </p>

                        <a href="assignment_download.php?id=<?= (int)$submission['id'] ?>">
                            <i class="fa-solid fa-download"></i>
                            View / Download Assignment
                        </a>

                    </article>
                <?php endforeach; ?>

            <?php endif; ?>

        </section>

    </div>
</div>

<script src="assignment.js"></script>
</body>
</html>
