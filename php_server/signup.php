<?php
session_start();
require_once 'db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    // Basic validation
    if (!$name || !$email || !$password || !$confirm_password) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Email already registered.";
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO user (name, email, password, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password_hash, $phone);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_name'] = $name;
                header("Location: scan.php"); // redirect to scan page after registration
                exit();
            } else {
                $message = "Error: Could not register user.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - E-Waste Reward</title>
<style>
body { font-family: Arial; text-align:center; margin-top:70px; background:#eef4fa; }
.card { background:#fff; padding:30px; display:inline-block; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
input { padding:10px; margin:5px; width:200px; border-radius:5px; border:1px solid #ccc; }
button { padding:10px 20px; margin-top:10px; border:none; border-radius:5px; background:#28a745; color:white; cursor:pointer; }
button:hover { background:#218838; }
.error { color:red; }
</style>
</head>
<body>
<div class="card">
    <h2>Register</h2>
    <?php if($message) echo "<p class='error'>$message</p>"; ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
        <input type="text" name="phone" placeholder="Phone (optional)"><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
