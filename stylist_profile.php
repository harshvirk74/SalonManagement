<?php
include 'session_connection.php';

// Get stylist ID from the query string
$stylist_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$stylist_id) {
    die("Invalid stylist ID.");
}

// Fetch stylist details
$getStylistDetails = "SELECT stylists.stylist_name, stylists.bio, users.username 
                      FROM stylists 
                      LEFT JOIN users ON stylists.user_id = users.user_id 
                      WHERE stylists.stylist_id = :id";
$statement = $db->prepare($getStylistDetails);
$statement->bindValue(':id', $stylist_id, PDO::PARAM_INT);
$statement->execute();
$stylist = $statement->fetch(PDO::FETCH_ASSOC);

if (!$stylist) {
    die("Stylist not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($stylist['stylist_name']); ?>'s Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
    <?php include('navbar.php'); ?>

    <div class="container mt-4">
        <h1 class="text-center text-primary"><?= htmlspecialchars($stylist['stylist_name']); ?></h1>
        <div class="card bg-secondary text-light mt-4">
            <div class="card-body">
                <h4>About <?= htmlspecialchars($stylist['stylist_name']); ?></h4>
                <p><?= htmlspecialchars($stylist['bio']); ?></p>
                <p class="text-muted"><strong>Username:</strong> <?= htmlspecialchars($stylist['username']); ?></p>
            </div>
        </div>
        <a href="stylists.php" class="btn btn-info mt-4">Back to Stylists</a>
    </div>
</body>
</html>
