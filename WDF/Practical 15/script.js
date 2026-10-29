// StudentHub - client-side scripts
document.addEventListener("DOMContentLoaded", function () {

  // Sidebar toggle (mobile)
  var menuBtn = document.getElementById("menuBtn");
  var sidebar = document.getElementById("sidebar");
  if (menuBtn && sidebar) {
    menuBtn.addEventListener("click", function () { sidebar.classList.toggle("open"); });
  }

  // Live course search
  var search = document.getElementById("search");
  var table = document.getElementById("courseTable");
  if (search && table) {
    search.addEventListener("input", function () {
      var q = search.value.toLowerCase();
      table.querySelectorAll("tr").forEach(function (row, i) {
        if (i === 0) return; // header
        row.style.display = row.textContent.toLowerCase().indexOf(q) > -1 ? "" : "none";
      });
    });
  }

  // Helpers for form validation
  function mark(input, ok) { input.classList.toggle("invalid", !ok); return ok; }
  function validEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }

  // Login form
  var loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      var ok = mark(document.getElementById("email"), validEmail(document.getElementById("email").value.trim()));
      ok = mark(document.getElementById("password"), document.getElementById("password").value.length > 0) && ok;
      if (!ok) { e.preventDefault(); alert("Please enter a valid email and password."); }
    });
  }

  // Register form
  var regForm = document.getElementById("registerForm");
  if (regForm) {
    regForm.addEventListener("submit", function (e) {
      var name = document.getElementById("name"), email = document.getElementById("email");
      var pass = document.getElementById("password"), conf = document.getElementById("confirm");
      var ok = mark(name, name.value.trim().length >= 3);
      ok = mark(email, validEmail(email.value.trim())) && ok;
      ok = mark(pass, pass.value.length >= 8 && /[A-Za-z]/.test(pass.value) && /\d/.test(pass.value)) && ok;
      ok = mark(conf, conf.value === pass.value) && ok;
      if (!ok) {
        e.preventDefault();
        alert("Check your details: name 3+ chars, valid email, password 8+ chars with letters and numbers, and matching confirmation.");
      }
    });
  }
});
