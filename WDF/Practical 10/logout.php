<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf($_POST['csrf'] ?? '')) {
    redirect(isLoggedIn() ? 'index.php' : 'login.php');
}

if (!empty($_COOKIE['remember_me']) && is_string($_COOKIE['remember_me'])) {
    $parts = explode(':', $_COOKIE['remember_me'], 2);
    $selector = $parts[0];

    $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE selector = ?");
    $stmt->bind_param("s", $selector);
    $stmt->execute();
    $stmt->close();
}

clearRememberCookie();
destroySession();

redirect('login.php?logout=1');