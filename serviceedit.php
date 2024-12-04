<?php
include 'session_connection.php';
include 'db_connect.php';




// Check if user is logged in; set role to "guest" for non-logged-in users
if (!isset($_SESSION['user_id'])) {
    $_SESSION['role'] = 'guest';
}

// Restrict editing to admin users only
$errorMessage = '';
if ($_SESSION['role'] !== 'admin') {
    $errorMessage = "You do not have permission to edit this service.";
}

// Validate and retrieve service ID
$service_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$service_id) {
    $errorMessage = "Invalid service ID.";
}

// Handle form submission for editing (only admins)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'admin') {
    $service_name = htmlspecialchars(trim($_POST['service_name']));
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $duration = filter_var($_POST['duration'], FILTER_VALIDATE_INT);

    if ($service_name && $price && $duration) {
        $stmt = DB()->prepare("UPDATE services SET service_name = ?, price = ?, duration = ? WHERE service_id = ?");
        $stmt->execute([$service_name, $price, $duration, $service_id]);
        header("Location: services.php");
        exit;
    } else {
        $errorMessage = "Please fill out all fields correctly.";
    }
}

// Fetch current service details
$stmt = DB()->prepare("SELECT * FROM services WHERE service_id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the service was found
if (!$service) {
    $errorMessage = "Service not found or invalid service ID.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Service</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

</head>
<body class="bg-dark text-light">
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <h2>Edit Service</h2>
        
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <?php if ($service && $_SESSION['role'] === 'admin'): ?>
            <!-- Edit form for admins -->
            <form action="serviceedit.php?id=<?= $service_id ?>" method="post">
                <div class="form-group">
                    <label for="service_name">Service Name:</label>
                    <input type="text" name="service_name" class="form-control" id="service_name" value="<?= htmlspecialchars($service['service_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" step="0.01" name="price" class="form-control" id="price" value="<?= htmlspecialchars($service['price']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="duration">Duration (minutes):</label>
                    <input type="number" name="duration" class="form-control" id="duration" value="<?= htmlspecialchars($service['duration']) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Service</button>
            </form>
        <?php elseif ($service): ?>
            <!-- Message for non-admin users -->
            <div class="alert alert-warning">
                You can view this service but cannot edit it.
            </div>
            <ul>
                <li><strong>Service Name:</strong> <?= htmlspecialchars($service['service_name']) ?></li>
                <li><strong>Price:</strong> $<?= htmlspecialchars($service['price']) ?></li>
                <li><strong>Duration:</strong> <?= htmlspecialchars($service['duration']) ?> minutes</li>
            </ul>
        <?php else: ?>
            <div class="alert alert-danger">Service not found. Please check the ID.</div>
        <?php endif; ?>
    </div>
</body>
</html>
