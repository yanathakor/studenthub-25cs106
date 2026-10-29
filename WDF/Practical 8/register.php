<?php

$conn = new mysqli("localhost", "root", "", "register");

if ($conn->connect_error) {
    die("Connection failed");
}

$fullName = $_POST["fullName"];
$email = $_POST["email"];
$mobile = $_POST["mobile"];
$gender = $_POST["gender"];
$password = $_POST["password"];
$confirmPassword = $_POST["confirmPassword"];

if ($password == $confirmPassword) {

    $sql = "INSERT INTO users
            (full_name, email, mobile, gender, password)
            VALUES
            ('$fullName', '$email', '$mobile', '$gender', '$password')";

    if ($conn->query($sql)) {

        echo "<script>
                alert('Your data is successfully stored');
                window.location='register.html';
              </script>";

    } else {

        echo "Error: " . $conn->error;

    }

} else {

    echo "Password and Confirm Password do not match";

}

$conn->close();

?>