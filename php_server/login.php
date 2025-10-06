<?php
session_start();
require_once 'db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, name, password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $message = "Email not found.";
    } else {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: scan.php"); // redirect to scan page
            exit();
        } else {
            $message = "Incorrect password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - E-Waste Reward</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            background: #eef4fa;
        }

        .card {
            background: #fff;
            padding: 20px;
            margin: 40px auto;
            width: 90%;
            max-width: 400px;
            /* max width for larger screens */
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        input {
            padding: 12px;
            margin: 8px 0;
            width: 90%;
            max-width: 300px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 16px;
        }

        button {
            padding: 12px 25px;
            margin-top: 12px;
            border: none;
            border-radius: 8px;
            background: #28a745;
            color: white;
            cursor: pointer;
            font-size: 16px;
            width: 90%;
            max-width: 300px;
        }

        button:hover {
            background: #218838;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* Responsive text & layout adjustments */
        @media screen and (max-width: 480px) {
            h2 {
                font-size: 20px;
            }

            input,
            button {
                font-size: 16px;
            }

            .card {
                padding: 15px;
                margin: 20px auto;
            }
        }
    </style>

</head>

<body>
    <div class="card">
        <h2>Login</h2>
        <?php if ($message) echo "<p class='error'>$message</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>

</html>