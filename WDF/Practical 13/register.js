function showError(input, spanId, message) {
    var span = document.getElementById(spanId);
    span.style.color = "red";
    span.innerHTML = message;
    input.style.borderColor = "red";
    input.focus();
}

function showValid(input, spanId) {
    document.getElementById(spanId).innerHTML = "";
    input.style.borderColor = "green";
}

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

    var valid = true;

    if (fullName.value.trim() == "") {
        showError(fullName, "s", "*Full Name is required");
        valid = false;
    }
    else if (!namePattern.test(fullName.value.trim())) {
        showError(fullName, "s", "*Name must contain alphabets only");
        valid = false;
    }
    else {
        showValid(fullName, "s");
    }

    if (email.value.trim() == "") {
        showError(email, "s1", "*Email is required");
        valid = false;
    }
    else if (!emailPattern.test(email.value.trim())) {
        showError(email, "s1", "*Enter valid email");
        valid = false;
    }
    else {
        showValid(email, "s1");
    }

    if (mobile.value.trim() == "") {
        showError(mobile, "s2", "*Mobile number is required");
        valid = false;
    }
    else if (!mobilePattern.test(mobile.value.trim())) {
        showError(mobile, "s2", "*Mobile number must contain numbers only");
        valid = false;
    }
    else if (mobile.value.trim().length != 10) {
        showError(mobile, "s2", "*Mobile number must be 10 digits");
        valid = false;
    }
    else {
        showValid(mobile, "s2");
    }

    if (gender.value == "") {
        alert("Please select gender");
        gender.style.borderColor = "red";
        valid = false;
    }
    else {
        gender.style.borderColor = "green";
    }

    if (password.value == "") {
        showError(password, "s3", "*Password is required");
        valid = false;
    }
    else if (password.value.length < 8) {
        showError(password, "s3", "*Password must be at least 8 characters");
        valid = false;
    }
    else if (!/[A-Za-z]/.test(password.value) || !/[0-9]/.test(password.value)) {
        showError(password, "s3", "*Password must contain letters and numbers");
        valid = false;
    }
    else {
        showValid(password, "s3");
    }

    if (confirmPassword.value == "") {
        showError(confirmPassword, "s4", "*Confirm Password is required");
        valid = false;
    }
    else if (confirmPassword.value != password.value) {
        showError(confirmPassword, "s4", "*Password does not match");
        valid = false;
    }
    else {
        showValid(confirmPassword, "s4");
    }

    return valid;
}