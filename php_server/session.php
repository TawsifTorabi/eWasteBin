<?php
// session.php
session_start();

// Simulate login or session data
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1234;
    $_SESSION['user_name'] = "Gazi Tawsif Turabi";
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

echo "Session Active for: " . htmlspecialchars($user_name) . " (ID: $user_id)";
