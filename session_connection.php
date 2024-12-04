<?php
// Start a session if not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions
require_once 'db_connect.php';
require_once 'registrationFunctions.php';

// Initialize the database connection and registration functions
$db = DB();
$app = new RegistrationFunctions();

// If a user is logged in, retrieve their details
if (!empty($_SESSION['user_id'])) {
    $user = $app->UserDetails($_SESSION['user_id']);
}
?>
