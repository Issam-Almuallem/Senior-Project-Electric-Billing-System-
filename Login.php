<?php
include 'db_connect.php';
session_start(); // Start the session

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Check the `users` table
    $query = "SELECT * FROM users WHERE Email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['Pass'])) {
            // Set session variables for user
            $_SESSION['user_id'] = $user['ID']; // Ensure `ID` matches your DB column
            $_SESSION['user_type'] = 'user';
            $_SESSION['loggedin'] = true;

            // Redirect to the user dashboard
            header("Location: index.php");
            exit();
        } else {
            echo "Incorrect password for user.";
        }
    } else {
        // Check the `admin` table
        $query = "SELECT * FROM admin WHERE Email = '$email'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin['Pass'])) {
                // Set session variables for admin
                $_SESSION['admin_id'] = $admin['Admin_ID'];
                $_SESSION['user_type'] = 'admin';
                $_SESSION['loggedin'] = true;

                // Redirect to the admin dashboard
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "Incorrect password for admin.";
            }
        } else {
            echo "No user or admin found with this email.";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="script.js"></script>
    <title>Login Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <style>
 * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Orbitron', sans-serif;
    background-color: #fff;
    color: #333;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Header for the logo */
header {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

/* Style for the logo container */
.logo-container {
    font-size: 100px;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}

.logo {
    position: relative;
    animation: lighting 1.5s infinite alternate, glow 1.5s ease-in-out infinite;
}

@keyframes lighting {
    0% {
        text-shadow: 0 0 5px rgba(255, 255, 255, 0.5), 0 0 10px rgba(255, 255, 255, 0.5), 0 0 15px rgba(255, 255, 255, 0.5);
        transform: scale(1);
    }
    100% {
        text-shadow: 0 0 20px rgba(255, 255, 255, 1), 0 0 30px rgba(255, 255, 255, 0.8), 0 0 40px rgba(255, 255, 255, 0.7);
        transform: scale(1.2);
    }
}

@keyframes glow {
    0% {
        color: #ffcc00;
        text-shadow: 0 0 5px rgba(255, 204, 0, 0.5), 0 0 10px rgba(255, 204, 0, 0.7), 0 0 15px rgba(255, 204, 0, 1);
    }
    50% {
        color: #ff6600;
        text-shadow: 0 0 20px rgba(255, 102, 0, 0.5), 0 0 30px rgba(255, 102, 0, 0.7), 0 0 40px rgba(255, 102, 0, 1);
    }
    100% {
        color: #ff0000;
        text-shadow: 0 0 25px rgba(255, 0, 0, 0.5), 0 0 35px rgba(255, 0, 0, 0.7), 0 0 50px rgba(255, 0, 0, 1);
    }
}

/* Main container */
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    width: 100%;
    max-width: 900px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

/* Form styling */
.form-container {
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 15px;
    text-align: center;
}

p {
    font-size: 14px;
    color: #666;
    text-align: center;
    margin-bottom: 25px;
}

/* Tabs */
.tabs {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    width: 100%;
}

.tab {
    background-color: #000;
    padding: 10px 20px;
    width: 48%;
    border: none;
    text-align: center;
    font-size: 16px;
    cursor: pointer;
    color: #fff;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.tab.active {
    background-color: #333;
    color: #fff;
}

.tab:hover {
    background-color: #444;
}

.form {
    display: block;
    width: 100%;
}

.hidden {
    display: none;
}

.input-group {
    margin-bottom: 15px;
    width: 100%;
}

label {
    font-size: 14px;
    color: #444;
    margin-bottom: 5px;
    display: block;
}

input {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    color: #333;
    background-color: #f1f1f1;
    border: 1px solid #ccc;
    border-radius: 6px;
    outline: none;
    transition: border 0.3s ease;
}

input:focus {
    border-color: #000;
}

.btn {
    width: 100%;
    padding: 12px;
    background-color: #000;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #333;
}

.switch-form {
    text-align: center;
    margin-top: 10px;
    font-size: 14px;
    color: #888;
}

.switch-form span {
    color: #000;
    cursor: pointer;
    text-decoration: underline;
}

/* Social buttons */
.social-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 15px;
    width: 100%;
}

.social-button {
    padding: 12px 20px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.google {
    background-color: #db4437;
    color: white;
}

.apple {
    background-color: #333;
    color: white;
}

.microsoft {
    background-color: #00a4ef;
    color: white;
}

.social-button:hover {
    opacity: 0.8;
}

/* Media Queries */
@media (max-width: 600px) {
    body {
        padding: 10px;
        height: auto;
    }

    .container {
        padding: 20px;
    }

    .tabs {
        flex-direction: column;
    }

    .tab {
        width: 100%;
        margin-bottom: 10px;
    }

    h2 {
        font-size: 20px;
    }

    input {
        font-size: 14px;
    }

    .btn {
        font-size: 14px;
    }
}

@media (min-width: 1200px) {
    .container {
        max-width: 1200px;
        padding: 40px;
    }

    h2 {
        font-size: 32px;
    }

    .tabs {
        gap: 20px;
    }

    .btn {
        font-size: 18px;
    }
}


    </style>
</head>

<body>

    <!-- Header with logo -->
    <header>
        <div class="logo-container">
            <i class="fas fa-bolt logo"></i>
        </div>
    </header>

    <!-- Form Container -->
    <div class="container">
        <div class="form-container">
            <h2>Welcome to Electric Billing</h2>
            <p>Please log in to your account or sign up if you're new.</p>


            <h1>Login</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form id="loginForm" class="form"  method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br><br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>
        <button type="submit" class="btn">Login</button>
    </form>
    <p class="mt-4 text-center switch-form">
    Don't have an account? 
    <a href="register.php" class="text-decoration-none text-success">
        <strong>Sign up</strong>
        <i class="fas fa-user-plus"></i>
    </a>
    <br>
    <br>
    <a href="forgot_password.php" class="text-decoration-none text-warning">
        <strong>Forgot Password?</strong>
        <i class="fas fa-key"></i>
    </a>
    </p>

        </div>
    </div>


</body>
</html>
