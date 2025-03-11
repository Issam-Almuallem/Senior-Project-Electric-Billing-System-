<?php
session_start(); // Start the session

// Destroy all session variables
session_unset();

// Destroy the session itself
session_destroy();

// Redirect to the homepage or login page after logging out
header("Location: index.php"); // You can change this to "Login.php" if you prefer
exit();
?>
