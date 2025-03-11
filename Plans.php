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
    $stmt = $conn->prepare("DELETE FROM plan WHERE Plan_ID = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success_message = "Plan deleted successfully!";
    } else {
        $error_message = "Failed to delete plan.";
    }
    $stmt->close();
}

// Handle addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plan'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $amperage = $_POST['amperage'];

    $stmt = $conn->prepare("INSERT INTO plan (Name, Price, Amperage) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $name, $price, $amperage);

    if ($stmt->execute()) {
        $success_message = "Plan added successfully!";
    } else {
        $error_message = "Failed to add plan.";
    }
    $stmt->close();
}

// Fetch plans
$sql = "SELECT Plan_ID, Name, Price, Amperage FROM plan";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plans Dashboard</title>
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

.btn-add {
    background-color: #28a745;
    color: #fff;
    margin-bottom: 20px;
    padding: 10px 15px;
}

.btn-add:hover {
    background-color: #218838;
}

.form-container {
    margin-bottom: 20px;
}

footer {
    text-align: center;
    padding: 20px 0;
    background-color: #1e3c72;
    color: #fff;
    position: fixed;
    width: 100%;
    bottom: 0;
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

/* Responsive Design */
@media (max-width: 768px) {
    header {
        padding: 15px;
    }

    header h1 {
        font-size: 1.8em;
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

    .btn-back, .btn-add {
        font-size: 14px;
        padding: 8px 12px;
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

    .btn-back, .btn-add {
        font-size: 12px;
        padding: 6px 10px;
    }

    footer {
        padding: 10px;
        font-size: 12px;
    }
}
#planFormContainer {
    margin-top: 20px;
    padding: 20px;
    border-radius: 8px;
    background: #f9fafc;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

#planFormContainer form label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
}

#planFormContainer input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

#planFormContainer button {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 4px;
    background-color: #28a745;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

#planFormContainer button:hover {
    background-color: #218838;
}

    </style>
</head>
<body>
    <header>
        <h1>Plans Dashboard</h1>
        <p>Manage all subscription plans</p>
    </header>
    <div class="container">
    <a href="admin_dashboard.php" class="btn-back">Back to Main Dashboard</a> <!-- Back Button -->

    <?php if (!empty($success_message)): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Button to toggle the form -->
    <button id="toggleFormBtn" class="btn btn-add">Add New Plan</button>

    <!-- Add Plan Form -->
    <div id="planFormContainer" style="display: none;" class="form-container">
        <form action="" method="POST">
            <label for="name">Plan Name:</label>
            <input type="text" name="name" id="name" placeholder="Plan Name" required>
            
            <label for="price">Price:</label>
            <input type="number" name="price" id="price" placeholder="Price" step="0.01" required>
            
            <label for="amperage">Amperage:</label>
            <input type="number" name="amperage" id="amperage" placeholder="Amperage" step="1" required>
            
            <button type="submit" name="add_plan" class="btn btn-add">Submit</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Amperage</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['Plan_ID']; ?></td>
                        <td><?php echo htmlspecialchars($row['Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Price']); ?></td>
                        <td><?php echo htmlspecialchars($row['Amperage']); ?></td>
                        <td>
                            <a href="edit_plan.php?id=<?php echo $row['Plan_ID']; ?>" class="btn btn-edit">Edit</a>
                            <a href="plans.php?delete_id=<?php echo $row['Plan_ID']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this plan?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No plans found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
    document.getElementById('toggleFormBtn').addEventListener('click', function () {
        const formContainer = document.getElementById('planFormContainer');
        if (formContainer.style.display === 'none' || formContainer.style.display === '') {
            formContainer.style.display = 'block';
        } else {
            formContainer.style.display = 'none';
        }
    });
</script>

    
</body>
</html>
