<?php
session_start();
include 'db_connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader (ensure PHPMailer is installed via Composer)
require 'vendor/autoload.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Function to send email
function sendEmail($email, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '12133482@students.liu.edu.lb'; // Replace with your email
        $mail->Password = 'yuop vydl afuq suti'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email details
        $mail->setFrom('12133482@students.liu.edu.lb', 'EBMS Platform'); // Replace with your email
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

// Handle deletion of subscriptions
if (isset($_GET['delete_subscription_id'])) {
    $delete_id = intval($_GET['delete_subscription_id']);

    // Fetch user email before deletion
    $stmt = $conn->prepare("
        SELECT users.Email, users.Fname, users.Lname 
        FROM subscriptions
        INNER JOIN users ON subscriptions.User_ID = users.id
        WHERE subscriptions.subsc_ID = ?
    ");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->bind_result($user_email, $user_fname, $user_lname);
    $stmt->fetch();
    $stmt->close();

    // Proceed with deletion
    $stmt = $conn->prepare("DELETE FROM subscriptions WHERE subsc_ID = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success_message = "Subscription deleted successfully!";
        
        // Send email notification
        $subject = "Subscription Cancellation Notification";
        $body = "Dear $user_fname $user_lname,<br><br>
                 Your subscription has been cancelled By our Team. If you have any questions, feel free to contact our support team.<br><br>
                 Best regards,<br>EBMS Platform";
        sendEmail($user_email, $subject, $body);
    } else {
        $error_message = "Failed to delete subscription.";
    }
    $stmt->close();
}

// Fetch payments with user details
$payments_sql = "
    SELECT 
        payments.PID, 
        payments.Amount, 
        payments.Date, 
        payments.payment_type, 
        users.Fname, 
        users.Lname
    FROM payments
    INNER JOIN users ON payments.User_ID = users.id
";
$payments_result = $conn->query($payments_sql);

$subscriptions_sql = "
    SELECT 
        subscriptions.subsc_ID, 
        subscriptions.Start_D, 
        subscriptions.End_D, 
        users.Fname, 
        users.Lname, 
        plan.Name AS Plan_Name
    FROM subscriptions
    INNER JOIN users ON subscriptions.User_ID = users.id
    INNER JOIN plan ON subscriptions.Plan_ID = plan.Plan_ID
";
$subscriptions_result = $conn->query($subscriptions_sql);

if (!$subscriptions_result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments & Subscriptions</title>
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
    margin-bottom: 30px;
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

    table {
        margin-bottom: 20px;
    }

    .btn {
        padding: 4px 8px;
        font-size: 12px;
    }

    .btn-back {
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
        padding: 6px;
        font-size: 11px;
    }

    .btn {
        font-size: 10px;
        padding: 3px 6px;
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
        <h1>Payments & Subscriptions</h1>
        <p>Manage all payments and subscriptions</p>
    </header>
    <div class="container">
    <a href="admin_dashboard.php" class="btn-back">Back to Main Dashboard</a>
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Payments Table -->
        <h2>Payments</h2>
        <table>
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Payment Type</th>
                    <th>User Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($payments_result->num_rows > 0): ?>
                    <?php while ($row = $payments_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['PID']; ?></td>
                            <td><?php echo htmlspecialchars($row['Amount']); ?></td>
                            <td><?php echo htmlspecialchars($row['Date']); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['Fname'] . ' ' . $row['Lname']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No payments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Subscriptions Table -->
        <h2>Subscriptions</h2>
        <table>
            <thead>
                <tr>
                    <th>Subscription ID</th>
                    <th>User Name</th>
                    <th>Plan Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($subscriptions_result->num_rows > 0): ?>
                    <?php while ($row = $subscriptions_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['subsc_ID']; ?></td>
                            <td><?php echo htmlspecialchars($row['Fname'] . ' ' . $row['Lname']); ?></td>
                            <td><?php echo htmlspecialchars($row['Plan_Name']); ?></td>
                            <td><?php echo htmlspecialchars($row['Start_D']); ?></td>
                            <td><?php echo htmlspecialchars($row['End_D']); ?></td>
                            <td>
                                <a href="edit_subscription.php?id=<?php echo $row['subsc_ID']; ?>" class="btn btn-edit">Edit</a>
                                <a href="?delete_subscription_id=<?php echo $row['subsc_ID']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this subscription?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No subscriptions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
