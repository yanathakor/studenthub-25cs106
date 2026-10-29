<?php

$conn = new mysqli("localhost", "root", "", "login");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$stored = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["txtname"]);
    $password = trim($_POST["txtpassword"]);

    if ($username != "" && $password != "") {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            echo "<script>
                alert('Your data is successfully stored');
                window.location='login.html';
              </script>";
        }
        else {
        echo "Error: " . $conn->error;
         }
        $stmt->close();
    }
}
$conn->close();
?>