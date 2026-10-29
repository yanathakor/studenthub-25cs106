function validateLoginForm() {
    var txtname = document.getElementById("txtname");
    var txtpassword = document.getElementById("txtpassword");

    var namePattern = /^[A-Za-z0-9@._-]+$/;
    var passwordPattern = /^[A-Za-z0-9]+$/;

    var valid = true;

    if (txtname.value.trim() == "") {
        document.getElementById("s").style.color = "red";
        document.getElementById("txtname").style.borderColor = "red";
        document.getElementById("s").innerHTML = "*Username is required";
        txtname.focus();
        valid = false;
    }
    else if (!namePattern.test(txtname.value)) {
        document.getElementById("s").style.color = "red";
        document.getElementById("txtname").style.borderColor = "red";
        document.getElementById("s").innerHTML = "*Invalid Username or Email";
        txtname.focus();
        valid = false;
    }
    else {
        document.getElementById("s").innerHTML = "";
        document.getElementById("txtname").style.borderColor = "green";
    }

    if (txtpassword.value.trim() == "") {
        document.getElementById("s1").style.color = "red";
        document.getElementById("txtpassword").style.borderColor = "red";
        document.getElementById("s1").innerHTML = "*Password is required";
        txtpassword.focus();
        valid = false;
    }
    else if (!passwordPattern.test(txtpassword.value)) {
        document.getElementById("s1").style.color = "red";
        document.getElementById("txtpassword").style.borderColor = "red";
        document.getElementById("s1").innerHTML = "*Password must contain numbers only";
        txtpassword.focus();
        valid = false;
    }
    else {
        document.getElementById("s1").innerHTML = "";
        document.getElementById("txtpassword").style.borderColor = "green";
    }

    if (valid) {
        alert("Login Successful");
        return true;
    }

    return false;
}

function togglePassword() {
    var txtpassword = document.getElementById("txtpassword");
    var showPassword = document.getElementById("showPassword");

    if (txtpassword && showPassword) {
        if (showPassword.checked) {
            txtpassword.type = "text";
        }
        else {
            txtpassword.type = "password";
        }
    }
}

window.onload = function () {
    var showPassword = document.getElementById("showPassword");

    if (showPassword) {
        showPassword.addEventListener("click", togglePassword);
    }
};

