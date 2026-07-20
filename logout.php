<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Unset all session variables
$_SESSION = array();

// 2. Erase the session cookie completely from the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
       // $params["secure"], $params["httponly"]
    );
}

// 3. Destroy the server session data
session_destroy();

// 4. Redirect back to the login screen
header("Location: index.php");
exit;