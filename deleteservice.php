<?php
include 'session_connection.php';
include 'db_connect.php';

// Start session and restrict access to logged-in users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
} 


// Check if the service_id is provided
$service_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($service_id) {
    // Prepare and execute the delete statement
    $stmt = DB()->prepare("DELETE FROM services WHERE service_id = ?");
    $stmt->execute([$service_id]);

    // Redirect back to the services page after deletion
    header("Location: services.php");
    exit;
} else {
    echo "Invalid service ID.";
}
?>
