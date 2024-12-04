<?php
session_start();
echo "Welcome to the Admin Dashboard, " . htmlspecialchars($_SESSION['username']) . "!";
?>
