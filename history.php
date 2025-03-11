<?php
session_start();

// Check if the user is logged in and if the user type is 'user'
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Database connection parameters
$host = 'localhost';
$dbname = 'ebms'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

// Create a connection to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

// Check if the consumption records were updated today
$today = date('Y-m-d');
if (!isset($_SESSION['last_update']) || $_SESSION['last_update'] !== $today) {
    // Update consumption records for the user
    $subscriptionsQuery = "
        SELECT subscriptions.User_ID, plan.Amperage
        FROM subscriptions
        JOIN plan ON subscriptions.Plan_ID = plan.Plan_ID
        WHERE subscriptions.User_ID = :user_id
    ";
    $stmt = $pdo->prepare($subscriptionsQuery);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($subscription) {
        $amperageLimit = $subscription['Amperage'];

        // Generate a random consumption value based on the amperage limit
        $randomConsumption = rand($amperageLimit * 0.1, $amperageLimit * 0.12);

        // Insert the random consumption value into the `consumptionrecords` table
        $insertQuery = "
            INSERT INTO consumptionrecords (Consumption, User_ID)
            VALUES (:consumption, :user_id)
        ";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->bindParam(':consumption', $randomConsumption, PDO::PARAM_INT);
        $insertStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $insertStmt->execute();
    }

    // Update the last update timestamp in the session
    $_SESSION['last_update'] = $today;
}

// Fetch the user's full name from the `users` table
$query = "
    SELECT Fname, Lname
    FROM users
    WHERE ID = :user_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Combine first name and last name
$full_name = $user['Fname'] . ' ' . $user['Lname'];

// Fetch payment history from `payments` table
$query = "
    SELECT payments.Amount, payments.Date, payments.payment_type
    FROM payments
    JOIN users ON payments.User_ID = users.ID
    WHERE users.ID = :user_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch consumption records from `consumptionrecords` table
$query = "
    SELECT consumptionrecords.Consumption
    FROM consumptionrecords
    WHERE consumptionrecords.User_ID = :user_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$consumption = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f4f7fc, #dfe3e9);
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #3f8efc, #6fa1ff);
            color: white;
            padding: 40px 20px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .dashboard {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 20px;
            border-bottom: 3px solid #3f8efc;
            padding-bottom: 10px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            color: #555;
            font-size: 1rem;
        }

        table th {
            background-color: #f0f4f9;
            font-weight: 600;
            color: #333;
        }

        table tr:hover {
            background-color: #f9f9f9;
            transition: background-color 0.3s ease-in-out;
        }

        .btn-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: space-between;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 15px;
            font-size: 1rem;
            border-radius: 50px;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: white;
            font-weight: 500;
        }

        .btn:hover {
            transform: translateY(-4px);
        }

        .logout-btn {
            background-color: #f44336;
        }

        .logout-btn:hover {
            background-color: #e53935;
        }

        .go-home-btn {
            background-color: #3f8efc;
        }

        .go-home-btn:hover {
            background-color: #3273d8;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 2rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            table th, table td {
                font-size: 0.9rem;
                padding: 12px;
            }

            .btn {
                font-size: 0.9rem;
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            .dashboard-header h1 {
                font-size: 1.6rem;
                padding: 20px;
            }

            h2 {
                font-size: 1.3rem;
                margin-bottom: 15px;
            }

            .dashboard {
                padding: 20px;
            }

            table {
                display: block;
                overflow-x: auto;
                font-size: 0.9rem;
            }

            table th, table td {
                font-size: 0.85rem;
                padding: 10px;
            }

            .btn-container {
                flex-direction: column;
                gap: 10px;
            }

            .btn {
                font-size: 0.85rem;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="dashboard-header">
        <h1><?php echo $full_name; ?>'s History</h1>
    </div>
    <div class="dashboard">
        <div class="table-container">
            <h2>Payment History</h2>
            <table>
                <thead>
                <tr>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Payment Type</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo number_format($payment['Amount'], 2); ?> USD</td>
                        <td><?php echo $payment['Date']; ?></td>
                        <td><?php echo ucfirst($payment['payment_type']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="table-container">
            <h2>Consumption Records</h2>
            <table>
                <thead>
                <tr>
                    <th>Consumption (kWh)</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($consumption as $record): ?>
                    <tr>
                        <td><?php echo $record['Consumption']; ?> kWh</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="btn-container">
            <a href="logout.php" class="btn logout-btn">Logout</a>
            <a href="index.php" class="btn go-home-btn">Go Back to Home</a>
        </div>
    </div>
</div>
</body>
</html>
