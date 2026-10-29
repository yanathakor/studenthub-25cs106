<?php

$stored = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullName = trim($_POST["fullName"]);
    $email = trim($_POST["email"]);
    $mobile = trim($_POST["mobile"]);
    $gender = trim($_POST["gender"]);
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirmPassword"]);

    if ($fullName != "" &&
        $email != "" &&
        $mobile != "" &&
        $gender != "" &&
        $password != "" &&
        $confirmPassword != "" &&
        $password == $confirmPassword) {

        $file = fopen("register.csv", "a");

        if ($file) {

            if (filesize("register.csv") == 0) {
                fputcsv($file, array(
                    "Full Name",
                    "Email",
                    "Mobile",
                    "Gender",
                    "Password"
                ));
            }

            fputcsv($file, array(
                $fullName,
                $email,
                $mobile,
                $gender,
                $password
            ));

            fclose($file);

            $stored = true;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>StudentHub Register</title>

    <link rel="stylesheet" href="register.css">

</head>

<body>

<div class="container">

    <div class="register-box">

        <h1>Create Your Account</h1>

        <form method="POST"
              action="register.php"
              onsubmit="return validateRegisterForm()">

            <div class="form-group">

                <div class="field">

                    <input type="text"
                           name="fullName"
                           id="fullName"
                           placeholder="Full Name">

                    <span id="s"></span>

                </div>

                <div class="field">

                    <input type="email"
                           name="email"
                           id="email"
                           placeholder="Email Address">

                    <span id="s1"></span>

                </div>

            </div>

            <div class="form-group">

                <div class="field">

                    <input type="text"
                           name="mobile"
                           id="mobile"
                           placeholder="Mobile No.">

                    <span id="s2"></span>

                </div>

                <div class="field">

                    <select name="gender"
                            id="gender">

                        <option value="">Gender</option>

                        <option value="Male">Male</option>

                        <option value="Female">Female</option>

                        <option value="Other">Other</option>

                    </select>

                </div>

            </div>

            <div class="form-group">

                <div class="field password-box">

                    <input type="password"
                           name="password"
                           id="password"
                           placeholder="Password">

                    <span id="s3"></span>

                </div>

                <div class="field password-box">

                    <input type="password"
                           name="confirmPassword"
                           id="confirmPassword"
                           placeholder="Confirm Password">

                    <span id="s4"></span>

                </div>

            </div>

            <div class="button-box">

                <button type="submit"
                        id="registerButton">

                    Register

                </button>

            </div>

        </form>

    </div>

</div>

<?php

if ($stored) {

    echo "<script>
            alert('Your data is successfully stored');
          </script>";
}

?>

<script src="register.js"></script>

</body>

</html>