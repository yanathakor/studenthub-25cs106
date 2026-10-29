<?php

mysqli_report(MYSQLI_REPORT_OFF);

define('IDLE_TIMEOUT', 900);
define('ABSOLUTE_TIMEOUT', 28800);
define('REMEMBER_DAYS', 30);
define('MAX_ATTEMPTS', 5);
define('LOCK_MINUTES', 15);
define('IS_HTTPS', !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');

session_name('STUDENTHUB_SESSION');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => IS_HTTPS,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

$conn = new mysqli("localhost", "root", "", "studenthub");

if ($conn->connect_error) {
    http_response_code(500);
    die("Database connection failed");
}

$conn->set_charset("utf8mb4");

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header("Location: " . $url);
    exit;
}

function csrfToken()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verifyCsrf($token)
{
    return isset($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function currentRole()
{
    return $_SESSION['role'] ?? null;
}

function setRememberCookie($value, $expires)
{
    setcookie('remember_me', $value, [
        'expires'  => $expires,
        'path'     => '/',
        'secure'   => IS_HTTPS,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

function clearRememberCookie()
{
    setRememberCookie('', time() - 3600);
    unset($_COOKIE['remember_me']);
}

function createRememberToken($conn, $userId)
{
    $selector  = bin2hex(random_bytes(9));
    $validator = bin2hex(random_bytes(32));
    $hash      = hash('sha256', $validator);
    $days      = REMEMBER_DAYS;

    $stmt = $conn->prepare("INSERT INTO remember_tokens (user_id, selector, token_hash, expires_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL ? DAY))");
    $stmt->bind_param("issi", $userId, $selector, $hash, $days);
    $stmt->execute();
    $stmt->close();

    setRememberCookie($selector . ':' . $validator, time() + REMEMBER_DAYS * 86400);
}

function loginUser($id, $name, $role)
{
    session_regenerate_id(true);
    unset($_SESSION['timed_out'], $_SESSION['csrf']);
    $_SESSION['user_id']       = (int)$id;
    $_SESSION['user_name']     = $name;
    $_SESSION['role']          = $role;
    $_SESSION['login_time']    = time();
    $_SESSION['last_activity'] = time();
}

function destroySession()
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'domain'   => $p['domain'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => 'Lax'
        ]);
    }

    session_destroy();
}

function autoLoginFromCookie($conn)
{
    if (empty($_COOKIE['remember_me']) || !is_string($_COOKIE['remember_me'])) {
        return false;
    }

    $parts = explode(':', $_COOKIE['remember_me'], 2);

    if (count($parts) !== 2 || !ctype_xdigit($parts[0]) || !ctype_xdigit($parts[1])) {
        clearRememberCookie();
        return false;
    }

    $selector  = $parts[0];
    $validator = $parts[1];

    $stmt = $conn->prepare("SELECT t.id, t.user_id, t.token_hash, u.full_name, u.role FROM remember_tokens t JOIN users u ON u.id = t.user_id WHERE t.selector = ? AND t.expires_at > NOW() LIMIT 1");
    $stmt->bind_param("s", $selector);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        clearRememberCookie();
        return false;
    }

    if (!hash_equals($row['token_hash'], hash('sha256', $validator))) {
        $userId = (int)$row['user_id'];
        $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
        clearRememberCookie();
        return false;
    }

    $tokenId = (int)$row['id'];
    $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE id = ?");
    $stmt->bind_param("i", $tokenId);
    $stmt->execute();
    $stmt->close();

    loginUser($row['user_id'], $row['full_name'], $row['role']);
    createRememberToken($conn, (int)$row['user_id']);

    return true;
}

function requireLogin()
{
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');

    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function requireRole(array $roles)
{
    requireLogin();

    if (!in_array(currentRole(), $roles, true)) {
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Access Denied</title></head><body style="font-family:Arial,sans-serif;text-align:center;margin-top:100px;"><h1>403 - Access Denied</h1><p>You do not have permission to view this page.</p><a href="index.php">Back to Home</a></body></html>';
        exit;
    }
}

if (isset($_SESSION['user_id'])) {
    $now  = time();
    $idle = $now - ($_SESSION['last_activity'] ?? 0);
    $age  = $now - ($_SESSION['login_time'] ?? 0);

    if ($idle > IDLE_TIMEOUT || $age > ABSOLUTE_TIMEOUT) {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['timed_out'] = true;
    } else {
        $_SESSION['last_activity'] = $now;
    }
}

if (!isset($_SESSION['user_id'])) {
    autoLoginFromCookie($conn);
}

if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT full_name, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $_SESSION['user_name'] = $row['full_name'];
        $_SESSION['role']      = $row['role'];
    } else {
        $_SESSION = [];
        session_regenerate_id(true);
    }
}