<?php
include 'session_connection.php';

$register_error_message = '';
$register_success_message = '';

if (!empty($_POST['btnRegister'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($username == "") {
        $register_error_message = 'Username field is required.';
    } elseif ($password == "") {
        $register_error_message = 'Password field is required.';
    } elseif ($password !== $confirm_password) {
        $register_error_message = 'Passwords do not match.';
    } else {
        // Call the Register function to create a new user
        $register = $app->Register($username, $password);

        if ($register['status']) {
            $register_success_message = $register['message'];
        } else {
            $register_error_message = $register['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
    <?php include('navbar.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-5 well m-auto pt-2">
                <h4>Register</h4>

                <!-- Display success message -->
                <?php if ($register_success_message != ""): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($register_success_message) ?></div>
                <?php endif; ?>

                <!-- Display error message -->
                <?php if ($register_error_message != ""): ?>
                    <div class="alert alert-danger"><strong>Error: </strong> <?= htmlspecialchars($register_error_message) ?></div>
                <?php endif; ?>

                <!-- Registration form -->
                <form action="register.php" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" id="username" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="password" required />
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" id="confirm_password" required />
                    </div>
                    <div class="form-group">
                        <input type="submit" name="btnRegister" class="btn btn-primary" value="Register" />
                    </div>
                </form>
                <p>Already have an account? <a href="login.php">Login here</a>.</p>
            </div>
        </div>
    </div>

    
</body>
</html>
