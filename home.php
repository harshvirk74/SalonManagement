<?php
include 'session_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hairstylist Winnipeg CMS</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body class="bg-dark text-light">
    <?php include('navbar.php'); ?>
    <div class="jumbotron jumbotron-fluid bg-secondary text-light text-center">
        <div class="container">
            <h1 class="display-4">Hairstylist Winnipeg</h1>
            <p class="lead">Personalized hairstyling services tailored to you. Discover our services, meet our stylists, and read client reviews.</p>
        </div>
    </div>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body text-dark">
                        <h5 class="card-title">Our Services</h5>
                        <p class="card-text">Browse and manage our range of hairstyling services, from cuts to colors and specialty treatments.</p>
                        <a href="services.php" class="btn btn-primary">Explore Services</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body text-dark">
                        <h5 class="card-title">Meet Our Stylists</h5>
                        <p class="card-text">Get to know our talented team of stylists, view their profiles, and choose the right stylist for you.</p>
                        <a href="stylists.php" class="btn btn-primary">View Stylists</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body text-dark">
                        <h5 class="card-title">Client Reviews</h5>
                        <p class="card-text">See what our clients are saying about us and leave your feedback to help us improve.</p>
                        <a href="feedback.php" class="btn btn-primary">Read Reviews</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
