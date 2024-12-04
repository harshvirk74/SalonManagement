<?php

// Define database connection constants only if they are not already defined
if (!defined('DB_DSN')) {
    define('DB_DSN', 'mysql:host=localhost;port=3307;dbname=cms_project;charset=utf8'); // Replace 'cms_project' with your actual database name if different
}

if (!defined('DB_USER')) {
    define('DB_USER', 'root');  // Default XAMPP username for MySQL
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');  // Default XAMPP password for MySQL is empty
}

// Check if the DB function is already defined to prevent redeclaration
if (!function_exists('DB')) {
    function DB() {
        static $db = null; // Use a static variable to store the PDO instance

        if ($db === null) { // Only create a new instance if one doesn’t already exist
            try {
                $db = new PDO(DB_DSN, DB_USER, DB_PASS); // Create a new PDO instance
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exception
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());  // Display error and halt execution
            }
        }

        return $db; // Return the existing PDO instance if already created
    }
}

?>
