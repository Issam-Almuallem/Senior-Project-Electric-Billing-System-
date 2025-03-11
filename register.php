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

        /* Font Awesome icon */
        .logo {
            position: relative;
            animation: lighting 1.5s infinite alternate, glow 1.5s ease-in-out infinite;
        }

        /* Lighting animation */
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

        /* Glow effect */
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

        /* Main container for form */
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

        /* Form container styles */
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

        /* Tab navigation */
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

        /* Additional login options */
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


        .social-button:hover {
            opacity: 0.8;
        }

        .social-button:focus {
            outline: none;
        }

        /* Mobile responsiveness */
        @media (max-width: 900px) {
            .container {
                flex-direction: column;
                padding: 10px;
            }

            h2 {
                font-size: 24px;
            }

            .tabs {
                flex-direction: column;
                width: 100%;
            }

            .tab {
                width: 100%;
                margin-bottom: 10px;
            }

            .input-group {
                margin-bottom: 15px;
            }

            .btn {
                font-size: 18px;
            }
        }

        a.text-primary {
            transition: color 0.3s ease;
        }

        a.text-primary:hover {
            color: #0056b3;
            /* Darker blue */
            text-decoration: underline;
            /* Underline on hover */
        }
    </style>
</head>

<body>

    <!-- Header with logo -->
    <header>
   <br>
   <br>
    </header>

    <!-- Form Container -->
    <div class="container">
        <div class="form-container">
           
        <?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form inputs
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    
    // Validate and hash the password
    $password = $_POST['password'];
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "Password must be at least 8 characters long and contain both letters and numbers.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT); 

        // Use prepared statement to avoid SQL injection
        $stmt = $conn->prepare("INSERT INTO users (Fname, Lname, email, phone, address, pass) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $fname, $lname, $email, $phone, $address, $hashed_password);
        
        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $conn->error;
        }
        
        $stmt->close();
    }
}
?>
   <body>
    <h1>Signup</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form id="signupForm" class="form" action="#" method="POST">
        <label for="fname">First Name:</label>
        <input type="text" name="fname" id="fname" required><br><br>

        <label for="lname">Last Name:</label>
        <input type="text" name="lname" id="lname" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br><br>

        <label for="phone">Phone Number:</label>
        <input type="tel" name="phone" id="phone" required><br><br>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required minlength="8"
               pattern="(?=.*[A-Za-z])(?=.*[0-9]).{8,}"><br>
        <small>Password must be at least 8 characters long and contain letters and numbers.</small><br><br>

        <button type="submit" class="btn">Sign Up</button>
    </form>
    <p class="mt-4 text-center">
        Already have an account?
        <a href="login.php" class="text-decoration-none text-primary">
            <strong>Login</strong>
            <i class="fas fa-sign-in-alt"></i>
        </a>
    </p>
</body>

</html>