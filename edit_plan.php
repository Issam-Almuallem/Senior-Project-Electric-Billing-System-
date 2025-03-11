<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch the plan details for editing
if (isset($_GET['id'])) {
    $plan_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT Plan_ID, Name, Price, Amperage FROM plan WHERE Plan_ID = ?");
    $stmt->bind_param("i", $plan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $plan = $result->fetch_assoc();
    } else {
        die("Plan not found.");
    }
    $stmt->close();
} else {
    die("Invalid request.");
}

// Handle the update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_plan'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $amperage = $_POST['amperage'];

    $stmt = $conn->prepare("UPDATE plan SET Name = ?, Price = ?, Amperage = ? WHERE Plan_ID = ?");
    $stmt->bind_param("sdii", $name, $price, $amperage, $plan_id);

    if ($stmt->execute()) {
        $success_message = "Plan updated successfully!";
        header("Location: plans.php");
        exit();
    } else {
        $error_message = "Failed to update plan.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Plan</title>
    <style>
   /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #eef2f7;
    color: #333;
}

.container {
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 1.8em;
}

form {
    display: flex;
    flex-direction: column;
}

input, button {
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

button {
    background-color: #007BFF;
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background-color: #0056b3;
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

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        max-width: 90%;
        margin: 30px auto;
        padding: 15px;
    }

    h1 {
        font-size: 1.5em;
    }

    input, button {
        padding: 8px;
        font-size: 14px;
    }

    button {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .container {
        max-width: 95%;
        margin: 20px auto;
        padding: 10px;
    }

    h1 {
        font-size: 1.2em;
    }

    input, button {
        padding: 6px;
        font-size: 12px;
    }

    button {
        font-size: 12px;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Plan</h1>

        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="name">Plan Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($plan['Name']); ?>" required>

            <label for="price">Price</label>
            <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($plan['Price']); ?>" step="0.01" required>

            <label for="amperage">Amperage</label>
            <input type="number" name="amperage" id="amperage" value="<?php echo htmlspecialchars($plan['Amperage']); ?>" required>

            <button type="submit" name="update_plan">Update Plan</button>
            
        </form>
    </div>
</body>
</html>
