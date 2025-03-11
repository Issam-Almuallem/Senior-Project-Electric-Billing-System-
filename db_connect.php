<?php
$host = "localhost"; // Server name
$dbname = "ebms"; // Your database name
$username = "root"; // Default for XAMPP/WAMP
$password = ""; // Default for XAMPP/WAMP

// Establish connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
