<?php
include 'session_connection.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Get user ID from GET parameter
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        header("Location: all_users.php");
        exit;
    }

    // Attempt to delete the user
    $delete = $app->deleteUser($user_id);

    // Redirect back to All Users page with a success or error message
    header("Location: all_users.php");
    exit;
} else {
    header("Location: all_users.php");
    exit;
}
?>
