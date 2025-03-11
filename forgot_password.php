<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'db_connect.php';
session_start();

$message = ""; // Initialize message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);

    // Check if the email exists
    $query = "SELECT * FROM users WHERE Email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(32)); // Generate a secure token

        // Store token and related information in the session
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_expiry'] = time() + 3600; // Token expires in 1 hour

        // Generate the approval link dynamically for local development
        $serverHost = $_SERVER['HTTP_HOST'];
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']); // Get current directory
        $approvalLink = "http://$serverHost$scriptDir/reset_password.php?token=$token";

        // Configure PHPMailer
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = ''; // Your Gmail address
            $mail->Password = ''; // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email Content
            $mail->setFrom('', 'Admin');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request Approval";

            // Button in the email
            $mail->Body = "
                <p>You have requested to reset your password. Click the button below to approve:</p>
                <p>
                    <a href='$approvalLink' style='
                        display: inline-block;
                        padding: 10px 20px;
                        font-size: 16px;
                        color: #fff;
                        background-color: #28a745;
                        text-decoration: none;
                        border-radius: 5px;
                    '>Approve Password Reset</a>
                </p>
                <p><strong>Note:</strong> This link will expire in 1 hour.</p>
            ";

            $mail->send();
            $message = "<div class='message success'>A password reset approval link has been sent to your email.</div>";
        } catch (Exception $e) {
            $message = "<div class='message error'>Failed to send email. Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        $message = "<div class='message error'>No user found with this email address.</div>";
    }
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validate the token and expiry in the session
    if (isset($_SESSION['reset_token']) && $_SESSION['reset_token'] === $token) {
        if (time() < $_SESSION['reset_expiry']) {
            $_SESSION['reset_approved'] = true; // Approve the reset
            $message = "<div class='message success'>Your reset link has been approved. You may now set a new password.</div>";
        } else {
            $message = "<div class='message error'>The reset link has expired. Please request a new one.</div>";
            unset($_SESSION['reset_token'], $_SESSION['reset_expiry']);
        }
    } else {
        $message = "<div class='message error'>Invalid reset link. Please check your email or request a new one.</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f7fc;
            padding: 20px;
        }

        .container {
            max-width: 400px;
            width: 100%;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 14px;
            color: #555;
            text-align: left;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
        }

        button {
            padding: 10px 15px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .note {
            font-size: 12px;
            color: #888;
            margin-top: 10px;
        }

        .message {
            margin-top: 15px;
            font-size: 14px;
            color: #555;
            text-align: left;
        }

        .message.success {
            color: #28a745;
        }

        .message.error {
            color: #dc3545;
        }

        @media (max-width: 400px) {
            h1 {
                font-size: 20px;
            }

            button {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Forgot Password</h1>
        <?php
        $message = ""; // Initialize message variable

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
            $email = $conn->real_escape_string($_POST['email']);

            // Check if the email exists
            $query = "SELECT * FROM users WHERE Email = '$email'";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $message = "<div class='message success'>A password reset approval link has been sent to your email.</div>";
            } else {
                $message = "<div class='message error'>No user found with this email address.</div>";
            }
        }

        if (isset($_SESSION['reset_approved']) && $_SESSION['reset_approved']): ?>
            <!-- Reset Password Form -->
            <form method="POST" action="reset_password.php">
                <label for="password">Enter New Password:</label>
                <input type="password" name="password" id="password" placeholder="New Password" required>
                <button type="submit">Reset Password</button>
            </form>
        <?php else: ?>
            <!-- Email Submission Form -->
            <form method="POST">
                <label for="email">Enter your email address:</label>
                <input type="email" name="email" id="email" placeholder="example@domain.com" required>
                <button type="submit">Submit</button>
            </form>
        <?php endif; ?>

        <!-- Display Message -->
        <?php echo $message; ?>

        <p class="note">Ensure you use the correct email associated with your account.</p>
    </div>
</body>

</html>
