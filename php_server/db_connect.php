<?php
// db_connect.php
$servername = "localhost";   // your MySQL server
$username = "root";          // your MySQL username
$password = "";              // your MySQL password
$dbname = "inference_db";    // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset to utf8mb4 for proper encoding
$conn->set_charset("utf8mb4");
?>
