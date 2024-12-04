<?php
include 'session_connection.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$update_error_message = '';
$update_success_message = '';

// Get user ID from GET parameter
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $user_details = $app->UserDetails($user_id);

    if (!$user_details) {
        header("Location: all_users.php");
        exit;
    }
} else {
    header("Location: all_users.php");
    exit;
}

// Handle form submission
if (!empty($_POST['btnUpdate'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role_id = intval($_POST['role_id']);

    if ($username == "") {
        $update_error_message = 'Username field is required.';
    } else {
        // Attempt to update the user
        $update = $app->updateUser($user_id, $username, $password, $role_id);

        if ($update['status']) {
            $update_success_message = $update['message'];
            // Refresh user details
            $user_details = $app->UserDetails($user_id);
        } else {
            $update_error_message = $update['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
    <?php include('navbar.php'); ?>

    <div class="container">
        <div class="row">
            <div class="col-md-5 well m-auto pt-2">
                <h4>Edit User</h4>

                <!-- Display success message -->
                <?php if ($update_success_message != ""): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($update_success_message) ?></div>
                <?php endif; ?>

                <!-- Display error message -->
                <?php if ($update_error_message != ""): ?>
                    <div class="alert alert-danger"><strong>Error: </strong> <?= htmlspecialchars($update_error_message) ?></div>
                <?php endif; ?>

                <!-- Edit User form -->
                <form action="edit_user.php?user_id=<?= $user_id ?>" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" id="username" value="<?= htmlspecialchars($user_details->username) ?>" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" id="password" />
                    </div>
                    <div class="form-group">
                        <label for="role_id">Role</label>
                        <select name="role_id" class="form-control" id="role_id">
                            <option value="1" <?= $user_details->role_id == 1 ? 'selected' : '' ?>>Admin</option>
                            <option value="2" <?= $user_details->role_id == 2 ? 'selected' : '' ?>>User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="btnUpdate" class="btn btn-primary" value="Update" />
                    </div>
                </form>
                <a href="all_users.php" class="btn btn-secondary">Back to All Users</a>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS (Optional) -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
</body>
</html>
