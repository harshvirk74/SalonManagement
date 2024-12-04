<?php
include 'session_connection.php';
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Determine if the user is an admin
$isAdmin = $_SESSION['role'] === 'admin';

// Handle sorting parameters
$sort_column = $_GET['sort_column'] ?? 'service_name'; // Default sorting column
$sort_order = $_GET['sort_order'] ?? 'ASC';           // Default sorting order

// Validate sorting inputs to prevent SQL injection
$allowed_columns = ['service_name', 'price', 'duration', 'created_at', 'updated_at'];
$allowed_order = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'service_name';
}

if (!in_array($sort_order, $allowed_order)) {
    $sort_order = 'ASC';
}

// Fetch services with sorting
$query = "SELECT service_id, service_name, price, duration, created_at, updated_at 
          FROM services 
          ORDER BY $sort_column $sort_order";
$statement = $db->prepare($query);
$statement->execute();
$services = $statement->fetchAll(PDO::FETCH_ASSOC);

// Generate success message for current sorting
$sorting_message = "Sorted by " . ucfirst(str_replace('_', ' ', $sort_column)) . " in " . ($sort_order === 'ASC' ? 'Ascending' : 'Descending') . " order.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
    <?php include('navbar.php'); ?>

    <div class="container mt-4">
        <h1 class="text-light">List of Services</h1>

        <!-- Success Message -->
        <div class="alert alert-success" role="alert">
            <?= $sorting_message ?>
        </div>

        <!-- Dropdown for Sorting -->
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <label for="sort_column" class="form-label">Sort By:</label>
                    <select name="sort_column" id="sort_column" class="form-control">
                        <option value="service_name" <?= $sort_column === 'service_name' ? 'selected' : '' ?>>Title</option>
                        <option value="price" <?= $sort_column === 'price' ? 'selected' : '' ?>>Price</option>
                        <option value="duration" <?= $sort_column === 'duration' ? 'selected' : '' ?>>Duration</option>
                        <option value="created_at" <?= $sort_column === 'created_at' ? 'selected' : '' ?>>Created At</option>
                        <option value="updated_at" <?= $sort_column === 'updated_at' ? 'selected' : '' ?>>Updated At</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="sort_order" class="form-label">Order:</label>
                    <select name="sort_order" id="sort_order" class="form-control">
                        <option value="ASC" <?= $sort_order === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                        <option value="DESC" <?= $sort_order === 'DESC' ? 'selected' : '' ?>>Descending</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Apply Sorting</button>
        </form>

        <?php if (empty($services)): ?>
            <h2 class="text-center text-light">There are no services available.</h2>
        <?php else: ?>
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td>
                                <a href="service_comments.php?id=<?= $service['service_id'] ?>" class="text-primary">
                                    <?= htmlspecialchars($service['service_name']) ?>
                                </a>
                            </td>
                            <td>$<?= htmlspecialchars(number_format($service['price'], 2)) ?></td>
                            <td><?= htmlspecialchars($service['duration']) ?> minutes</td>
                            <td><?= htmlspecialchars($service['created_at']) ?></td>
                            <td><?= htmlspecialchars($service['updated_at']) ?></td>
                            <td>
                                <a href="service_comments.php?id=<?= $service['service_id'] ?>" class="btn btn-info btn-sm">View Comments</a>
                                <?php if ($isAdmin): ?>
                                    <a href="serviceedit.php?id=<?= $service['service_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="deleteservice.php?id=<?= $service['service_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
