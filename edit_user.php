<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id'])) {
    header("Location: subs.php");
    exit();
}

$user_id = intval($_GET['id']);
$success_message = $error_message = "";

// Fetch user data
$stmt = $conn->prepare("SELECT Fname, Lname, Email, Address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $error_message = "User not found.";
} else {
    $user = $result->fetch_assoc();
}

$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = htmlspecialchars(trim($_POST['Fname']));
    $lname = htmlspecialchars(trim($_POST['Lname']));
    $email = htmlspecialchars(trim($_POST['Email']));
    $address = htmlspecialchars(trim($_POST['Address']));

    $stmt = $conn->prepare("UPDATE users SET Fname = ?, Lname = ?, Email = ?, Address = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $fname, $lname, $email, $address, $user_id);

    if ($stmt->execute()) {
        $success_message = "User updated successfully!";
        $stmt->close();
        // Refresh user data
        $stmt = $conn->prepare("SELECT Fname, Lname, Email, Address FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    } else {
        $error_message = "Failed to update user.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
   /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #eef2f7;
    color: #333;
}

header {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    color: #fff;
    padding: 20px 0;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

header h1 {
    margin: 0;
    font-size: 2em;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    font-size: 16px;
}

.btn-primary {
    background-color: #007BFF;
    color: #fff;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: #fff;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.success-message {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.error-message {
    background: #f8d7da;
    color: #721c24;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
}

/* Footer */
footer {
    text-align: center;
    padding: 20px 0;
    background-color: #1e3c72;
    color: #fff;
    position: fixed;
    width: 100%;
    bottom: 0;
    box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    header {
        padding: 15px;
    }

    header h1 {
        font-size: 1.8em;
    }

    .container {
        max-width: 90%;
        padding: 15px;
        margin: 15px auto;
    }

    .form-group input {
        padding: 8px;
        font-size: 14px;
    }

    .btn {
        padding: 8px 12px;
        font-size: 14px;
    }

    footer {
        padding: 15px;
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    header {
        padding: 10px;
    }

    header h1 {
        font-size: 1.5em;
    }

    .container {
        max-width: 95%;
        padding: 10px;
        margin: 10px auto;
    }

    .form-group input {
        padding: 6px;
        font-size: 12px;
    }

    .btn {
        padding: 6px 10px;
        font-size: 12px;
    }

    footer {
        padding: 10px;
        font-size: 12px;
    }
}


    </style>
</head>
<body>
    <header>
        <h1>Edit User</h1>
        <p>Update user details</p>
    </header>
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="Fname">First Name</label>
                <input type="text" id="Fname" name="Fname" value="<?php echo htmlspecialchars($user['Fname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="Lname">Last Name</label>
                <input type="text" id="Lname" name="Lname" value="<?php echo htmlspecialchars($user['Lname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="Email">Email</label>
                <input type="email" id="Email" name="Email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="Address">Address</label>
                <input type="text" id="Address" name="Address" value="<?php echo htmlspecialchars($user['Address']); ?>" required>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="subs.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>
    <footer>
        &copy; <?php echo date("Y"); ?> Electric Billing Admin Panel. All Rights Reserved.
    </footer>
</body>
</html>
