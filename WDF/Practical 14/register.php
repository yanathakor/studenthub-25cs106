<?php
require 'config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

function fail($message)
{
    echo "<script>alert(" . json_encode($message) . "); window.location='register.html';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect('register.html');
}

$fullName        = trim($_POST["fullName"] ?? "");
$email           = strtolower(trim($_POST["email"] ?? ""));
$mobile          = trim($_POST["mobile"] ?? "");
$gender          = trim($_POST["gender"] ?? "");
$password        = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";

if ($fullName === "" || $email === "" || $mobile === "" || $gender === "" || $password === "" || $confirmPassword === "") {
    fail("Please fill in all fields.");
}

if (!preg_match('/^[A-Za-z ]+$/', $fullName)) {
    fail("Name must contain alphabets only.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail("Enter a valid email.");
}

if (!preg_match('/^[0-9]{10}$/', $mobile)) {
    fail("Mobile number must be 10 digits.");
}

if (!in_array($gender, ["Male", "Female", "Other"], true)) {
    fail("Please select a valid gender.");
}

if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
    fail("Password must be at least 8 characters and contain a letter and a number.");
}

if ($password !== $confirmPassword) {
    fail("Password and Confirm Password do not match.");
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (full_name, email, mobile, gender, password, role) VALUES (?, ?, ?, ?, ?, 'student')");
$stmt->bind_param("sssss", $fullName, $email, $mobile, $gender, $hashedPassword);

if (!$stmt->execute()) {
    if ($stmt->errno === 1062) {
        fail("This email is already registered.");
    }
    fail("Registration failed. Please try again.");
}

$stmt->close();
$conn->close();

redirect('login.php?registered=1');