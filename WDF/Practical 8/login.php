
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

        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            $stored = true;
        }

        $stmt->close();
    }
}

$conn->close();

?>
