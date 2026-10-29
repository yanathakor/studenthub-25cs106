function validateLoginForm() {

    var username = document.getElementById("id");
    var password = document.getElementById("password");

    if (username.value == "") {
        document.getElementById("s").innerHTML = "*Username is required";
        username.focus();
        return false;
    }

    if (password.value == "") {
        document.getElementById("s1").innerHTML = "*Password is required";
        password.focus();
        return false;
    }

    alert("Login Successful");

    location.reload();

    return false;
}

function togglePassword() {

    var password = document.getElementById("password");
    var show = document.getElementById("showPassword");

    if (show.checked) {
        password.type = "text";
    } else {
        password.type = "password";
    }
}