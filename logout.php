<?php
require_once 'init_session.php'; // Include để start session

// Xóa tất cả session vars
$_SESSION = [];
// Xóa cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
// Destroy session
session_destroy();
header('location: login.php');
exit;
