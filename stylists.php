<?php
include 'session_connection.php';

// Fetch all stylists from the database
$getStylists = "SELECT stylists.stylist_id, stylists.stylist_name, stylists.bio, users.username 
                FROM stylists 
                LEFT JOIN users ON stylists.user_id = users.user_id";
$statement = $db->prepare($getStylists);
$statement->execute();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Our Stylists</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body class="bg-dark text-light">
    <!-- Navigation bar -->
    <?php include('navbar.php'); ?>

    <!-- Header -->
    <div class="container mt-4">
        <h1 class="text-center text-primary">Our Stylists</h1>
        <p class="text-center">Meet our talented stylists ready to transform your look.</p>
        
        <!-- If no stylists are found -->
        <?php if ($statement->rowCount() === 0): ?>
            <div class="alert alert-danger text-center">No stylists available at the moment.</div>
        <?php else: ?>
            <!-- Display stylists in a grid layout -->
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php while ($row = $statement->fetch()): ?>
                    <div class="col mb-4">
                        <div class="card bg-secondary text-light h-100">
                            <div class="card-body">
                                <h5 class="card-title text-primary text-center">
                                    <?= htmlspecialchars($row['stylist_name']); ?>
                                </h5>
                                <p class="card-text"><?= htmlspecialchars($row['bio']); ?></p>
                                <p class="text-muted"><strong>Username:</strong> <?= htmlspecialchars($row['username']); ?></p>
                                <a href="stylist_profile.php?id=<?= $row['stylist_id']; ?>" class="btn btn-info btn-block">View Profile</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
