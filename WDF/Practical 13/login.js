function validateLoginForm() {

    var username = document.getElementById("txtname");
    var password = document.getElementById("txtpassword");
    var role = document.getElementById("role");
    var s = document.getElementById("s");
    var s1 = document.getElementById("s1");
    var s2 = document.getElementById("s2");
    var valid = true;

    s.innerHTML = "";
    s1.innerHTML = "";
    s2.innerHTML = "";

    if (username.value.trim() === "") {
        s.style.color = "red";
        s.innerHTML = "*Email is required";
        username.focus();
        valid = false;
    }

    if (password.value === "") {
        s1.style.color = "red";
        s1.innerHTML = "*Password is required";
        if (valid) {
            password.focus();
        }
        valid = false;
    }

    if (role.value === "") {
        s2.style.color = "red";
        s2.innerHTML = "*Please select a role";
        if (valid) {
            role.focus();
        }
        valid = false;
    }

    return valid;
}

document.addEventListener("DOMContentLoaded", function () {

    var password = document.getElementById("txtpassword");
    var show = document.getElementById("showPassword");

    show.addEventListener("change", function () {
        password.type = show.checked ? "text" : "password";
    });

});