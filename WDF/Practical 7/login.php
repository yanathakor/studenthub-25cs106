<?php

$stored = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if ($username != "" && $password != "") {

        $file = fopen("users.csv", "a");

        if ($file) {

            fputcsv($file, array($username, $password));

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

    <title>StudentHub Login</title>

    <link rel="stylesheet" href="login.css">

</head>

<body>

    <div class="container">

        <div class="left">

            <h1>STUDENTHUB</h1>

            <h2>Welcome Back!</h2>

            <p>
                Manage your courses, assignments,<br>
                attendance and results in one place.
            </p>

        </div>


        <div class="right">

            <form method="POST"
                  action="login.php"
                  onsubmit="return validateLoginForm()">

                <h2>Login</h2>

                <div>

                    <input type="text"
                           name="username"
                           id="txtname"
                           placeholder="Email or Username">

                    <span id="s"></span>


                    <input type="password"
                           name="password"
                           id="txtpassword"
                           placeholder="Password">

                    <span id="s1"></span>


                    <div class="show-password">

                        <input type="checkbox"
                               id="showPassword">

                        <label for="showPassword">
                            Show Password
                        </label>

                    </div>


                    <button type="submit"
                            class="button">

                        Login

                    </button>


                    <a href="#">
                        Forgot Password?
                    </a>


                    <br><br>


                    <p>

                        Don't have an account?

                        <a href="register.html">
                            Register
                        </a>

                    </p>

                </div>

            </form>

        </div>

    </div>


    <?php

    if ($stored) {
        echo "<script>alert('Your data is successfully stored');</script>";
    }

    ?>


    <script src="login.js"></script>

</body>

</html>
