<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* Clear all session data */
$_SESSION = [];

/* Destroy the session completely */
session_destroy();

/* Optional: destroy session cookie */
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

redirect('admin_login.php');
