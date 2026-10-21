//login javascript
           
function validateLoginForm(){
    var txtname = document.getElementById("txtname");
    var txtpassword = document.getElementById("txtpassword");

     var str = /^[A-Za-z0-9]+$/;
     var str1 = /^[0-9]+$/;
     var count = 0;
    if (!str.test(txtname.value)) {
                    document.getElementById('s').style.color = "red";
                    document.getElementById('txtname').style.borderColor = "red";
                    document.getElementById('s').innerHTML = "*Invalid ID";
                    txtname.focus();
                    return false;
                }
                else{
                    document.getElementById('s').innerHTML = "";
                    document.getElementById('txtname').style.borderColor = "green";
                }

    if(!str1.test(txtpassword.value)){
        document.getElementById('s').style.color = "red";
        document.getElementById('txtpassword').style.borderColor = "red";
        document.getElementById('s1').innerHTML = "*Invalid Password";
        txtpassword.focus();
        return false;
    } 
    else {
        document.getElementById('s1').innerHTML = "";
        document.getElementById('txtpassword').style.borderColor = "green";
        count++;
    }
    if(count == 1){
        alert("Login Successful");
    }
}

function togglePassword() {
    var txtpassword = document.getElementById("txtpassword");
    var showPassword = document.getElementById("showPassword");

    if (txtpassword && showPassword) {
        txtpassword.type = showPassword.checked ? "text" : "password";
    }
}

window.onload = function () {
    var showPassword = document.getElementById("showPassword");

    if (showPassword) {
        showPassword.addEventListener("click", togglePassword);
    }
}