function validateForm() {

var f=document.getElementById("firstName");
var d=document.getElementById("day");
var m=document.getElementById("month");
var y=document.getElementById("year");
var mob=document.getElementById("mobile");
var e=document.getElementById("email");
var p=document.getElementById("password");
var c=document.getElementById("course");
var ph=document.getElementById("photo");
var city=document.getElementById("city");
var a=document.getElementById("address");

var err=["nameError","dobError","mobileError","emailError","passwordError",
"genderError","departmentError","courseError","photoError","cityError",
"addressError"];

for(var i=0;i<err.length;i++)
document.getElementById(err[i]).innerHTML="";

f.style.borderColor=d.style.borderColor=m.style.borderColor=
y.style.borderColor=mob.style.borderColor=e.style.borderColor=
p.style.borderColor=c.style.borderColor=city.style.borderColor=
a.style.borderColor="#87BBA2";

function error(id,msg,obj){
document.getElementById(id).innerHTML=msg;
document.getElementById(id).style.color="red";
if(obj){obj.style.borderColor="red";obj.focus();}
return false;
}

if(f.value.trim()==="")
return error("nameError","*Invalid Name",f);

if(d.value.trim()===""||m.value.trim()===""||y.value.trim()==="")
return error("dobError","*Invalid Date of Birth",d);

if(!/^[0-9]{10}$/.test(mob.value.trim()))
return error("mobileError","*Invalid Mobile Number",mob);

if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e.value.trim()))
return error("emailError","*Invalid Email",e);

if(p.value.trim()==="")
return error("passwordError","*Invalid Password",p);

if(p.value.length<6)
return error("passwordError","*Password must be 6 characters",p);

if(!document.querySelector('input[name="gender"]:checked'))
return error("genderError","*Select Gender");

if(document.querySelectorAll('input[name="department"]:checked').length===0)
return error("departmentError","*Select Department");

if(c.value==="")
return error("courseError","*Select Course");

if(ph.value==="")
return error("photoError","*Upload Photo");

if(city.value.trim()==="")
return error("cityError","*Enter City",city);

if(a.value.trim()==="")
return error("addressError","*Enter Address",a);

alert("Registration Successful");
return true;
}