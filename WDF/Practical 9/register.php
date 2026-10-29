<?php

$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) {
    die("Connection failed");
}

function saveUserToCsv($data)
{
    $csvFile = __DIR__ . "/user.csv";
    $fileExists = file_exists($csvFile);
    $handle = fopen($csvFile, "a");

    if (!$handle) {
        return false;
    }

    if (!$fileExists) {
        fputcsv($handle, ["Full Name", "Email", "Mobile", "Gender", "Password"]);
    }

    fputcsv($handle, $data);
    fclose($handle);

    return true;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST["fullName"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $confirmPassword = trim($_POST["confirmPassword"] ?? "");

    if ($fullName !== "" && $email !== "" && $mobile !== "" && $gender !== "" && $password !== "" && $confirmPassword !== "") {
        if ($password === $confirmPassword) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (full_name, email, mobile, gender, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                echo "Error: " . $conn->error;
            } else {
                $stmt->bind_param("sssss", $fullName, $email, $mobile, $gender, $hashedPassword);

                if ($stmt->execute()) {
                    saveUserToCsv([$fullName, $email, $mobile, $gender, $password]);

                    echo "<script>
                            alert('Your data is successfully stored');
                            window.location='register.html';
                          </script>";
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            }
        } else {
            echo "Password and Confirm Password do not match";
        }
    } else {
        echo "Please fill in all fields.";
    }
}

$conn->close();
?>