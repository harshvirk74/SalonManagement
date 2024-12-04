<?php
// Start session
session_start();

// End session by unsetting specific session variables
unset($_SESSION['user_id']);
unset($_SESSION['user_logged_in']);
unset($_SESSION['username']);
unset($_SESSION['role']); // If you have a role session variable

// Destroy the session if you want to ensure all session data is cleared
session_destroy();

// Redirect to the login page or homepage
header("Location: login.php"); // Change 'login.php' to 'home.php' if you want to go to the homepage
exit;
?>
