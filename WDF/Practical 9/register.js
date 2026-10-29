function validateRegisterForm() {

    var fullName = document.getElementById("fullName");
    var email = document.getElementById("email");
    var mobile = document.getElementById("mobile");
    var gender = document.getElementById("gender");
    var password = document.getElementById("password");
    var confirmPassword = document.getElementById("confirmPassword");

    var namePattern = /^[A-Za-z ]+$/;
    var emailPattern = /^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
    var mobilePattern = /^[0-9]+$/;
    var passwordPattern = /^[0-9]+$/;

    var valid = true;

    if (fullName.value.trim() == "") {
        document.getElementById("s").style.color = "red";
        fullName.style.borderColor = "red";
        document.getElementById("s").innerHTML = "*Full Name is required";
        fullName.focus();
        valid = false;
    }
    else if (!namePattern.test(fullName.value.trim())) {
        document.getElementById("s").style.color = "red";
        fullName.style.borderColor = "red";
        document.getElementById("s").innerHTML = "*Name must contain alphabets only";
        fullName.focus();
        valid = false;
    }
    else {
        document.getElementById("s").innerHTML = "";
        fullName.style.borderColor = "green";
    }

    if (email.value.trim() == "") {
        document.getElementById("s1").style.color = "red";
        email.style.borderColor = "red";
        document.getElementById("s1").innerHTML = "*Email is required";
        email.focus();
        valid = false;
    }
    else if (!emailPattern.test(email.value.trim())) {
        document.getElementById("s1").style.color = "red";
        email.style.borderColor = "red";
        document.getElementById("s1").innerHTML = "*Enter valid email";
        email.focus();
        valid = false;
    }
    else {
        document.getElementById("s1").innerHTML = "";
        email.style.borderColor = "green";
    }

    if (mobile.value.trim() == "") {
        document.getElementById("s2").style.color = "red";
        mobile.style.borderColor = "red";
        document.getElementById("s2").innerHTML = "*Mobile number is required";
        mobile.focus();
        valid = false;
    }
    else if (!mobilePattern.test(mobile.value.trim())) {
        document.getElementById("s2").style.color = "red";
        mobile.style.borderColor = "red";
        document.getElementById("s2").innerHTML = "*Mobile number must contain numbers only";
        mobile.focus();
        valid = false;
    }
    else if (mobile.value.trim().length != 10) {
        document.getElementById("s2").style.color = "red";
        mobile.style.borderColor = "red";
        document.getElementById("s2").innerHTML = "*Mobile number must be 10 digits";
        mobile.focus();
        valid = false;
    }
    else {
        document.getElementById("s2").innerHTML = "";
        mobile.style.borderColor = "green";
    }

    if (gender.value == "") {
        alert("Please select gender");
        gender.style.borderColor = "red";
        valid = false;
    }
    else {
        gender.style.borderColor = "green";
    }

    if (password.value.trim() == "") {
        document.getElementById("s3").style.color = "red";
        password.style.borderColor = "red";
        document.getElementById("s3").innerHTML = "*Password is required";
        password.focus();
        valid = false;
    }
    else if (!passwordPattern.test(password.value)) {
        document.getElementById("s3").style.color = "red";
        password.style.borderColor = "red";
        document.getElementById("s3").innerHTML = "*Password must contain numbers only";
        password.focus();
        valid = false;
    }
    else if (password.value.length < 6) {
        document.getElementById("s3").style.color = "red";
        password.style.borderColor = "red";
        document.getElementById("s3").innerHTML = "*Password must be at least 6 digits";
        password.focus();
        valid = false;
    }
    else {
        document.getElementById("s3").innerHTML = "";
        password.style.borderColor = "green";
    }

    if (confirmPassword.value.trim() == "") {
        document.getElementById("s4").style.color = "red";
        confirmPassword.style.borderColor = "red";
        document.getElementById("s4").innerHTML = "*Confirm Password is required";
        confirmPassword.focus();
        valid = false;
    }
    else if (confirmPassword.value != password.value) {
        document.getElementById("s4").style.color = "red";
        confirmPassword.style.borderColor = "red";
        document.getElementById("s4").innerHTML = "*Password does not match";
        confirmPassword.focus();
        valid = false;
    }
    else {
        document.getElementById("s4").innerHTML = "";
        confirmPassword.style.borderColor = "green";
    }

    if (valid) {
        return true;
    }

    return false;
}