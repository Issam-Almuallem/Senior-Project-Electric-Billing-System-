<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if subscription ID is provided
if (!isset($_GET['id'])) {
    die("Subscription ID is missing.");
}

$subscription_id = intval($_GET['id']);
$success_message = "";
$error_message = "";

// Fetch subscription details
$subscription_sql = "
    SELECT 
        subscriptions.subsc_ID, 
        subscriptions.Start_D, 
        subscriptions.End_D, 
        subscriptions.Plan_ID, 
        users.Fname, 
        users.Lname 
    FROM subscriptions
    INNER JOIN users ON subscriptions.User_ID = users.id
    WHERE subscriptions.subsc_ID = ?
";
$stmt = $conn->prepare($subscription_sql);
$stmt->bind_param("i", $subscription_id);
$stmt->execute();
$subscription_result = $stmt->get_result();

if ($subscription_result->num_rows === 0) {
    die("Subscription not found.");
}

$subscription = $subscription_result->fetch_assoc();
$stmt->close();

// Fetch all plans for dropdown
$plans_sql = "SELECT Plan_ID, Name FROM plan";
$plans_result = $conn->query($plans_sql);

if (!$plans_result) {
    die("Query failed: " . $conn->error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $plan_id = $_POST['plan_id'];

    // Update subscription details
    $update_sql = "UPDATE subscriptions SET Start_D = ?, End_D = ?, Plan_ID = ? WHERE subsc_ID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssii", $start_date, $end_date, $plan_id, $subscription_id);

    if ($update_stmt->execute()) {
        $success_message = "Subscription updated successfully!";
    } else {
        $error_message = "Failed to update subscription: " . $conn->error;
    }
    $update_stmt->close();

    // Refresh subscription details
    header("Location: Bill.php?id=$subscription_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subscription</title>
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
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

input, select, button {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

button {
    background: #2a5298;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #1e3c72;
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

    input, select, button {
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

    input, select, button {
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
        
        <h1>Edit Subscription</h1>
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="user_name">User Name</label>
                <input type="text" id="user_name" value="<?php echo htmlspecialchars($subscription['Fname'] . ' ' . $subscription['Lname']); ?>" disabled>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($subscription['Start_D']); ?>" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($subscription['End_D']); ?>" required>
            </div>

            <div class="form-group">
                <label for="plan_id">Plan</label>
                <select id="plan_id" name="plan_id" required>
                    <?php while ($plan = $plans_result->fetch_assoc()): ?>
                        <option value="<?php echo $plan['Plan_ID']; ?>" <?php echo $plan['Plan_ID'] == $subscription['Plan_ID'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($plan['Name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit">Update Subscription</button>
        </form>
    </div>
</body>
</html>
