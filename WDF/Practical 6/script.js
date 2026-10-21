//register javascript

            function validateRegisterForm(){
                var fullName = document.getElementById("fullName");
                var email = document.getElementById("email");
                var mobile = document.getElementById("mobile");
                var password = document.getElementById("password");
                var confirmPassword = document.getElementById("confirmPassword");

                var str = /^[A-Za-z]+$/;
                var str1 = /^[0-9]+$/;
                var alphnum = /[a-zA-Z0-9]+$/;
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                var count=0;

                if (!str.test(fullName.value)) {
                    document.getElementById('s').style.color = "red";
                    document.getElementById('fullName').style.borderColor = "red";
                    document.getElementById('s').innerHTML = "*Invalid name";
                    fullName.focus();
                    return false;
                }
                else{
                    document.getElementById('s').innerHTML = "";
                    document.getElementById('fullName').style.borderColor = "green";
                }

                if(!emailPattern.test(email.value)){
                    document.getElementById('s1').style.color = "red";
                    document.getElementById('email').style.borderColor = "red";
                    document.getElementById('s1').innerHTML = "*Invalid email";
                    email.focus();
                    return false;
                }
                else{
                    document.getElementById('s1').innerHTML = "";
                    document.getElementById('email').style.borderColor = "green";
                }

                if (!str1.test(mobile.value)) {
                    document.getElementById('s2').style.color = "red";
                    document.getElementById('mobile').style.borderColor = "red";
                    document.getElementById('s2').innerHTML = "*Invalid mobile number";
                    mobile.focus();
                    return false;
                }
                else{
                    document.getElementById('s2').innerHTML = "";
                    document.getElementById('mobile').style.borderColor = "green";
                }

                if (!alphnum.test(password.value)) {
                    document.getElementById('s3').style.color = "red";
                    document.getElementById('password').style.borderColor = "red";
                    document.getElementById('s3').innerHTML = "*Invalid password";
                    password.focus();
                    return false;
                }
                else{
                    document.getElementById('s3').innerHTML = "";
                    document.getElementById('password').style.borderColor = "green";
                }

                if (password.value !== confirmPassword.value) {
                    document.getElementById('s4').style.color = "red";
                    document.getElementById('confirmPassword').style.borderColor = "red";
                    document.getElementById('s4').innerHTML = "*Passwords do not match";
                    confirmPassword.focus();
                    return false;
                }
                else{
                    document.getElementById('s4').innerHTML = "";
                    document.getElementById('confirmPassword').style.borderColor = "green";
                    count++;
                }

                if(count==1){
                    alert("Registration successful");
                }

            }
// Show / Hide Password

function togglePassword(passwordId, eyeId) {

    var password = document.getElementById(passwordId);
    var eye = document.getElementById(eyeId);

    if (password.type === "password") {

        password.type = "text";
        eye.innerHTML = "🙈";

    } else {

        password.type = "password";
        eye.innerHTML = "👁";

    }
}
// Load saved theme
if (localStorage.getItem("theme") === "dark") {

    document.body.classList.add("dark-mode");

    themeButton.innerHTML = "☀️";

}


// Change theme
themeButton.addEventListener("click", function () {

    document.body.classList.toggle("dark-mode");


    if (document.body.classList.contains("dark-mode")) {

        localStorage.setItem("theme", "dark");

        themeButton.innerHTML = "☀️";

    } else {

        localStorage.setItem("theme", "light");

        themeButton.innerHTML = "🌙";

    }

});

// Open / Close FAQ Panel
function toggleFAQ() {
    const faqPanel = document.getElementById("faqPanel");

    if (faqPanel.style.display === "block") {
        faqPanel.style.display = "none";
    } else {
        faqPanel.style.display = "block";
    }
}

// Open / Close individual FAQ question
function toggleQuestion(button) {
    const answer = button.nextElementSibling;
    const icon = button.querySelector("span");

    if (answer.style.display === "block") {
        answer.style.display = "none";
        icon.textContent = "+";
    } else {
        answer.style.display = "block";
        icon.textContent = "−";
    }
}

