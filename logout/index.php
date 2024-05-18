<?php
session_start();

// Clear all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Delete the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    // Print the session cookie params for debugging
    print_r($params);
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Delete custom cookies
setcookie('username', '', time() - 3600, '/');
setcookie('user_id', '', time() - 3600, '/');

// Redirect to the home page
header('Location: ../');
exit();