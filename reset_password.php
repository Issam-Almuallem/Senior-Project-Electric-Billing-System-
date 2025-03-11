<?php
include 'db_connect.php';
session_start();

$message = ""; // Variable to hold the status message

if (!isset($_SESSION['reset_expiry']) || time() > $_SESSION['reset_expiry']) {
    $message = "The reset link has expired.";
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_SESSION['reset_email'] ?? null;

    if ($email) {
        // Update the user's password
        $query = "UPDATE users SET Pass = '$new_password' WHERE Email = '$email'";
        if ($conn->query($query)) {
            $message = "Your password has been reset successfully.";
            unset($_SESSION['reset_token'], $_SESSION['reset_email'], $_SESSION['reset_expiry']);
        } else {
            $message = "Failed to reset the password.";
        }
    } else {
        $message = "Invalid session data.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 400px;
            width: 90%;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #555;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 8px;
            color: #666;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 10px;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            margin-top: 15px;
            font-size: 14px;
            color: #333;
        }

        .back-button {
            background: #6c757d;
            color: #fff;
            text-decoration: none;
            display: inline-block;
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 5px;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }

        .back-button:hover {
            background: #5a6268;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 20px;
            }

            label {
                font-size: 12px;
            }

            input[type="password"] {
                font-size: 12px;
            }

            button, .back-button {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        <form method="POST">
            <label for="password">Enter your new password:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Reset Password</button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <a href="login.php" class="back-button">Back to Login</a>
    </div>
</body>
</html>
