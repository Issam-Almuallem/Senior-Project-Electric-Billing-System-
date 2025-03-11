<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success_message = "User deleted successfully!";
    } else {
        $error_message = "Failed to delete user.";
    }
    $stmt->close();
}

// Handle addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    // Collect input data
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Ensure this is hashed before storing
    $phone = $_POST['phone'] ?? null;
    $address = $_POST['address'];
    $pid = $_POST['pid'] ?? null;
    $credit_num = $_POST['credit_num'] ?? null;

    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO users (Fname, Lname, Email, Pass, Phone, Address, PID, Credit_num) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssis", $fname, $lname, $email, $hashed_password, $phone, $address, $pid, $credit_num);

    // Execute and check for success
    if ($stmt->execute()) {
        $success_message = "User added successfully!";
    } else {
        $error_message = "Failed to add user. Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch users
$sql = "SELECT id, Fname, Lname, Email, Address FROM users";
$result = $conn->query($sql);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribers Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    text-align: left;
    padding: 10px;
    border: 1px solid #ddd;
    word-wrap: break-word;
}

table th {
    background: #f4f4f4;
}

table tr:hover {
    background: #f1f7fc;
}

.btn {
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
}

.btn-edit {
    background-color: #007BFF;
    color: #fff;
}

.btn-edit:hover {
    background-color: #0056b3;
}

.btn-delete {
    background-color: #dc3545;
    color: #fff;
}

.btn-delete:hover {
    background-color: #a71d2a;
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

.btn-back {
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    background-color: #28a745;
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    cursor: pointer;
}

.btn-back:hover {
    background-color: #218838;
}

#toggleFormBtn {
    padding: 10px 20px;
    background-color: #2a5298;
    color: #fff;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-bottom: 20px;
}

#userFormContainer {
    display: none;
    margin-top: 20px;
    padding: 20px;
    border-radius: 8px;
    background: #f9fafc;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

input, textarea, button {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

button {
    background: #2a5298;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

/* Responsive Design */
@media (max-width: 768px) {
    header h1 {
        font-size: 1.5em;
    }

    .container {
        padding: 15px;
        margin: 10px;
    }

    table th, table td {
        padding: 8px;
        font-size: 12px;
    }

    .btn {
        padding: 4px 8px;
        font-size: 12px;
    }

    #toggleFormBtn {
        font-size: 14px;
        padding: 8px 16px;
    }

    .btn-back {
        font-size: 14px;
        padding: 8px 12px;
    }
}

@media (max-width: 480px) {
    header h1 {
        font-size: 1.2em;
    }

    .container {
        padding: 10px;
        margin: 5px;
    }

    table {
        display: block;
        overflow-x: auto;
        width: 100%;
    }

    table th, table td {
        white-space: nowrap;
    }

    .btn {
        font-size: 10px;
        padding: 3px 6px;
    }

    #toggleFormBtn {
        font-size: 12px;
        padding: 6px 12px;
    }

    .btn-back {
        font-size: 12px;
        padding: 6px 10px;
    }
}

    </style>
</head>
<body>
    <header>
        <h1>Subscribers Dashboard</h1>
        <p>Manage all registered users and their subscriptions</p>
    </header>
    <div class="container">
    <a href="admin_dashboard.php" class="btn-back">Back to Main Dashboard</a> <!-- Back Button -->
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

  <table> 
       <!-- Button to toggle the form -->
       <button id="toggleFormBtn">Add New User</button>

       <div id="userFormContainer" style="display: none;">
    <form method="POST" action="">
        <label for="fname">First Name:</label>
        <input type="text" name="fname" id="fname" required>

        <label for="lname">Last Name:</label>
        <input type="text" name="lname" id="lname" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" id="phone">

        <label for="address">Address:</label>
        <textarea name="address" id="address" required></textarea>

        <button type="submit" name="add_user">Add User</button>
    </form>
</div>


            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['Fname']); ?></td>
                            <td><?php echo htmlspecialchars($row['Lname']); ?></td>
                            <td><?php echo htmlspecialchars($row['Email']); ?></td>
                            <td><?php echo htmlspecialchars($row['Address']); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>
                                <a href="subs.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script>
    // Toggle the visibility of the form
    document.getElementById('toggleFormBtn').addEventListener('click', function () {
        const userFormContainer = document.getElementById('userFormContainer');
        if (userFormContainer.style.display === 'none' || userFormContainer.style.display === '') {
            userFormContainer.style.display = 'block';
        } else {
            userFormContainer.style.display = 'none';
        }
    });
</script>

</body>
</html>
