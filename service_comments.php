<?php
include 'session_connection.php';
include 'db_connect.php';

$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch service details
$query = "SELECT * FROM services WHERE service_id = :service_id";
$statement = $db->prepare($query);
$statement->bindValue(':service_id', $service_id, PDO::PARAM_INT);
$statement->execute();
$service = $statement->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    die("Service not found.");
}

// Fetch comments in reverse chronological order
$commentsQuery = "SELECT * FROM feedback WHERE service_id = :service_id ORDER BY created_at DESC";
$commentsStmt = $db->prepare($commentsQuery);
$commentsStmt->bindValue(':service_id', $service_id, PDO::PARAM_INT);
$commentsStmt->execute();
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

// Ensure guests can access
if (!isset($_SESSION['user_id'])) {
    $_SESSION['role'] = 'guest'; // Default role for guests
}

// Handle comment submission
$error_message = '';
$user_input = ['username' => '', 'comment' => '']; // To retain user input if CAPTCHA fails

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $captcha = $_POST['captcha'] ?? '';
    $user_input['username'] = $_POST['username'] ?? ($_SESSION['username'] ?? '');
    $user_input['comment'] = $_POST['comment'] ?? '';

    if ($captcha !== $_SESSION['captcha']) {
        $error_message = 'Incorrect CAPTCHA. Please try again.';
    } else {
        $username = isset($_SESSION['user_id']) ? $_SESSION['username'] : filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $client_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        $insertQuery = $db->prepare("INSERT INTO feedback (service_id, client_id, username, comment) VALUES (:service_id, :client_id, :username, :comment)");
        $insertQuery->bindValue(':service_id', $service_id, PDO::PARAM_INT);
        $insertQuery->bindValue(':client_id', $client_id, PDO::PARAM_INT);
        $insertQuery->bindValue(':username', $username, PDO::PARAM_STR);
        $insertQuery->bindValue(':comment', $comment, PDO::PARAM_STR);
        $insertQuery->execute();

        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $service_id);
        exit;
    }
}

// Handle comment moderation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['comment_id'])) {
    $comment_id = intval($_POST['comment_id']);
    if ($_SESSION['role'] === 'admin' && $_POST['action'] === 'delete') {
        $deleteQuery = $db->prepare("DELETE FROM feedback WHERE feedback_id = :comment_id");
        $deleteQuery->bindValue(':comment_id', $comment_id, PDO::PARAM_INT);
        $deleteQuery->execute();
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $service_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($service['service_name']) ?> - Comments</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h1><?= htmlspecialchars($service['service_name']) ?></h1>
        <p><strong>Price:</strong> $<?= htmlspecialchars($service['price']) ?></p>
        <p><strong>Duration:</strong> <?= htmlspecialchars($service['duration']) ?> minutes</p>

        <!-- Display comments -->
        <h2>Comments</h2>
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <strong><?= htmlspecialchars($comment['username']) ?>:</strong>
                <p><?= htmlspecialchars($comment['comment']) ?></p>
                <small><?= htmlspecialchars($comment['created_at']) ?></small>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="comment_id" value="<?= $comment['feedback_id'] ?>">
                        <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Add comment form -->
        <h2>Leave a Comment</h2>
        <form method="POST">
            <?php if ($_SESSION['role'] === 'guest'): ?>
                <div class="form-group">
                    <label for="username">Your Name</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($user_input['username']) ?>" required>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="comment">Comment</label>
                <textarea name="comment" id="comment" class="form-control" rows="4" required><?= htmlspecialchars($user_input['comment']) ?></textarea>
            </div>
            <!-- CAPTCHA -->
            <div class="form-group">
                <label for="captcha">Enter the text from the image:</label>
                <img src="comments.php" alt="CAPTCHA">
                <input type="text" name="captcha" id="captcha" class="form-control" required>
            </div>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <button type="submit" name="add_comment" class="btn btn-primary">Submit Comment</button>
        </form>
    </div>
</body>
</html>
