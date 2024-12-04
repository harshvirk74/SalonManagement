
    <?php
    include 'session_connection.php';
    include 'db_connect.php';



    // Start session and restrict access to logged-in admin users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}


    $statusMessage = ''; // Variable to store status messages

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $service_name = htmlspecialchars(trim($_POST['service_name']));
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $duration = filter_var($_POST['duration'], FILTER_VALIDATE_INT);

        if ($service_name && $price && $duration) {
            $stmt = DB()->prepare("INSERT INTO services (service_name, price, duration) VALUES (?, ?, ?)");
            $stmt->execute([$service_name, $price, $duration]);
            $statusMessage = "Service added successfully!";
        } else {
            $statusMessage = "Please fill out all fields correctly.";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Add New Service</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    </head>
    <body class="bg-dark text-light">
        <?php include('navbar.php'); ?>
        <div class="container">
            <h2>Add a New Service</h2>
            <?php if ($statusMessage): ?>
                <div class="alert alert-info"><?= htmlspecialchars($statusMessage) ?></div>
            <?php endif; ?>
            <form action="newservice.php" method="post">
                <div class="form-group">
                    <label for="service_name">Service Name:</label>
                    <input type="text" name="service_name" class="form-control" id="service_name" required>
                </div>
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" step="0.01" name="price" class="form-control" id="price" required>
                </div>
                <div class="form-group">
                    <label for="duration">Duration (minutes):</label>
                    <input type="number" name="duration" class="form-control" id="duration" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Service</button>
            </form>
        </div>
    </body>
    </html>
