<?php
include 'session_connection.php';

$login_error_message = '';

if (!empty($_POST['btnLogin'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username == "") {
        $login_error_message = 'Username field is required.';
    } elseif ($password == "") {
        $login_error_message = 'Password field is required.';
    } else {
        // Call the Login function to verify credentials
        $login = $app->Login($username, $password);

        if ($login) {
            // Get user data from the login result
            $user_id = $login['user_id'];  // Using 'user_id' based on standard naming
            $role_id = $login['role_id'];  // Check role_id for admin permissions

            if ($user_id > 0) {
                // Store session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role_id == 1 ? 'admin' : 'user';  // Set role based on role_id

                header("Location: profile.php"); // Redirect to profile/dashboard page
                exit;
            }
        } else {
            $login_error_message = 'Invalid login details.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
    <?php include('navbar.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-5 well m-auto pt-2">
                <h4>Login</h4>
                
                <!-- Display any login error messages -->
                <?php if ($login_error_message != ""): ?>
                    <div class="alert alert-danger"><strong>Error: </strong> <?= htmlspecialchars($login_error_message) ?></div>
                <?php endif; ?>
                
                <!-- Login form -->
                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="username">Username/Email</label>
                        <input type="text" name="username" class="form-control" id="username" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="password" required />
                    </div>
                    <div class="form-group">
                        <input type="submit" name="btnLogin" class="btn btn-primary" value="Login" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
