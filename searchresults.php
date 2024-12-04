<?php
include 'db_connect.php';

// Retrieve and sanitize inputs
$searchString = filter_input(INPUT_GET, 'searchbar', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
$category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'all';
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;

// Ensure page number is valid
if ($page < 1) {
    $page = 1;
}

// Define results per page and calculate offset
$results_per_page = 5; // Number of results per page
$offset = ($page - 1) * $results_per_page;

// Build SQL query dynamically based on category
$sql = "";
$params = ['search' => '%' . $searchString . '%'];

if ($category === 'stylists') {
    // Fetch stylists
    $sql = "SELECT stylist_name AS name, bio, stylist_id AS id 
            FROM stylists 
            WHERE stylist_name LIKE :search 
            LIMIT $results_per_page OFFSET $offset";
} elseif ($category === 'services') {
    // Fetch services
    $sql = "SELECT service_name AS name, duration, service_id AS id 
            FROM services 
            WHERE service_name LIKE :search 
            LIMIT $results_per_page OFFSET $offset";
} else {
    // Default: fetch both services and stylists
    $sql = "SELECT stylist_name AS name, bio, stylist_id AS id, 'stylists' AS type
            FROM stylists 
            WHERE stylist_name LIKE :search
            UNION
            SELECT service_name AS name, duration, service_id AS id, 'services' AS type
            FROM services 
            WHERE service_name LIKE :search
            LIMIT $results_per_page OFFSET $offset";
}

// Fetch results
$stmt = DB()->prepare($sql);
$stmt->bindValue(':search', $params['search'], PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total results for pagination
$countSql = "";
if ($category === 'stylists') {
    $countSql = "SELECT COUNT(*) FROM stylists WHERE stylist_name LIKE :search";
} elseif ($category === 'services') {
    $countSql = "SELECT COUNT(*) FROM services WHERE service_name LIKE :search";
} else {
    $countSql = "SELECT COUNT(*) FROM stylists WHERE stylist_name LIKE :search
                 UNION ALL
                 SELECT COUNT(*) FROM services WHERE service_name LIKE :search";
}
$countStmt = DB()->prepare($countSql);
$countStmt->bindValue(':search', $params['search'], PDO::PARAM_STR);
$countStmt->execute();
$totalResults = $countStmt->fetchColumn();
$totalPages = ceil($totalResults / $results_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <h1>Search Results</h1>
        <p>Showing results for: <strong><?= htmlspecialchars($searchString); ?></strong></p>

        <?php if (empty($results)): ?>
            <p>No results found.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($results as $result): ?>
                    <?php if ($category === 'stylists' || ($result['type'] ?? '') === 'stylists'): ?>
                        <li>
                            <a href="stylist_profile.php?id=<?= $result['id']; ?>"><?= htmlspecialchars($result['name']); ?></a>
                            <p><?= htmlspecialchars($result['bio'] ?? ''); ?></p>
                        </li>
                    <?php elseif ($category === 'services' || ($result['type'] ?? '') === 'services'): ?>
                        <li>
                            <a href="service_details.php?id=<?= $result['id']; ?>"><?= htmlspecialchars($result['name']); ?></a>
                            <p>Duration: <?= htmlspecialchars($result['duration'] ?? ''); ?> minutes</p>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <!-- Previous Page Link -->
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?searchbar=<?= urlencode($searchString); ?>&category=<?= htmlspecialchars($category); ?>&page=<?= $page - 1; ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        <!-- Page Number Links -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?searchbar=<?= urlencode($searchString); ?>&category=<?= htmlspecialchars($category); ?>&page=<?= $i; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <!-- Next Page Link -->
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?searchbar=<?= urlencode($searchString); ?>&category=<?= htmlspecialchars($category); ?>&page=<?= $page + 1; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
