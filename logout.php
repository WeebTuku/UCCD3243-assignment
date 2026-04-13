<?php
session_start();

// Clear session
$_SESSION = array();

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Clear Remember Me cookie
setcookie('remember_email', '', time() - 3600, '/');

session_unset();
session_destroy();

header('Location: login.php');
exit();