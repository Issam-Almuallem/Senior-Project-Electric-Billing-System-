<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'db_connect.php';
session_start();
// Check if the user is logged in and if the user type is 'user'
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php");
    exit();
}

// Initialize variables for feedback messages
$message = "";
$user_id = $_SESSION['user_id']; // Retrieve the logged-in user ID

// Handle Payments and Subscriptions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment'])) {
    $planName = $conn->real_escape_string($_POST['planName']);
    $paymentMethod = $conn->real_escape_string($_POST['payment']);
    $cardNumber = isset($_POST['cardNumber']) ? $conn->real_escape_string($_POST['cardNumber']) : null;
    $cardType = isset($_POST['cardType']) ? $conn->real_escape_string($_POST['cardType']) : null;

    // Fetch Plan_ID and Price based on the selected plan name
    $planQuery = "SELECT Plan_ID, Price FROM plan WHERE Name = '$planName'";
    $planResult = $conn->query($planQuery);

    if ($planResult->num_rows > 0) {
        $plan = $planResult->fetch_assoc();
        $planId = $plan['Plan_ID'];
        $amountPaid = $plan['Price'];
    } else {
        $message = "Selected plan does not exist.";
        exit;
    }

    // Check if the user already subscribed this year
    $currentYear = date('Y');
    $subscriptionCheckQuery = "SELECT * FROM subscriptions WHERE User_ID = '$user_id' AND YEAR(Start_D) = '$currentYear'";
    $subscriptionCheckResult = $conn->query($subscriptionCheckQuery);

    if ($subscriptionCheckResult->num_rows > 0) {
        $message = "You have already subscribed to a plan this year.";
    } else {
        // Handle payment and subscription
        if ($paymentMethod === 'credit') {
            if (empty($cardNumber) || empty($cardType)) {
                $message = "Credit card details are required for payment.";
            } else {
                $creditCardQuery = "INSERT INTO creditcard (Credit_num, Balance, CreditCardType) VALUES ('$cardNumber', '0.00', '$cardType')";
                $paymentQuery = "INSERT INTO payments (Amount, Date, User_ID, payment_type) VALUES ('$amountPaid', CURDATE(), '$user_id', 'creditcard')";
                $conn->query($paymentQuery);
            }
        } elseif ($paymentMethod === 'paypal') {
            $paymentQuery = "INSERT INTO payments (Amount, Date, User_ID, payment_type) VALUES ('$amountPaid', CURDATE(), '$user_id', 'paypal')";
            $conn->query($paymentQuery);
        }

        // Add subscription
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 year'));
        $subscriptionQuery = "INSERT INTO subscriptions (User_ID, Plan_ID, Start_D, End_D) VALUES ('$user_id', '$planId', '$startDate', '$endDate')";
        if ($conn->query($subscriptionQuery) === TRUE) {
            $message = "Subscription and payment successfully processed.";

            // Send payment confirmation email
            $emailQuery = "SELECT Email FROM users WHERE ID = '$user_id'";
            $emailResult = $conn->query($emailQuery);
            if ($emailResult->num_rows > 0) {
                $userEmail = $emailResult->fetch_assoc()['Email'];

                // Configure PHPMailer
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = '12133484@students.liu.edu.lb'; // Replace with your email
                    $mail->Password = 'yuop vydl afuq suti'; // Replace with your app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Email details
                    $mail->setFrom('12133484@students.liu.edu.lb', 'EBMS Platform'); // Replace with your email
                    $mail->addAddress($userEmail);
                    $mail->isHTML(true);
                    $mail->Subject = "Payment Confirmation";
                    $mail->Body = "
                        <h3>Thank you for your payment!</h3>
                        <p>Here are your payment details:</p>
                        <ul>
                            <li><strong>Plan:</strong> $planName</li>
                            <li><strong>Amount Paid:</strong> $$amountPaid</li>
                            <li><strong>Payment Method:</strong> " . ucfirst($paymentMethod) . "</li>
                            <li><strong>Start Date:</strong> $startDate</li>
                            <li><strong>End Date:</strong> $endDate</li>
                        </ul>
                        <p>If you have any questions, feel free to contact us.</p>
                        <p>Regards,<br>EBMS Platform Team</p>
                    ";
                    $mail->send();
                    $message .= " A confirmation email has been sent to your email.";
                } catch (Exception $e) {
                    $message .= " However, we couldn't send the confirmation email. Error: {$mail->ErrorInfo}";
                }
            }
        }
    }
}

$conn->close(); // Close the database connection
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Payments</title>
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css"
        integrity="sha384-b6lVK+yci+bfDmaY1u0zE8YYJt0TZxLEAFyYSLHId4xoVvsrQu3INevFKo+Xir8e" crossorigin="anonymous" />
</head>

<body data-bs-theme="dark">
    <header>
        <a href="index.php" class="logo-link">
            <h1 class="logo">EBMS</h1>
        </a>
        <nav class="main-nav">
            <a href="index.php">Home</a>

            <a href="AboutUS.php">About Us</a>
            <a href="ContactUs.php">Contact Us</a>
            <a href="Payments.php">Payments</a>
            <a href="rating.php">Rate Us</a>
            <a href="history.php">History</a>

        </nav>

        <!-- Mobile Navigation Menu -->
        <nav id="mobileNav" class="mobile-nav">
            <a href="index.php">Home</a>

            <a href="AboutUS.php">About Us</a>
            <a href="ContactUs.php">Contact Us</a>
            <a href="Payments.php">Payments</a>
            <a href="rating.php">Rate US</a>
            <a href="history.php">History</a>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="Login.php">Login</a>
            <?php endif; ?>
        </nav>

        <!-- Desktop Login/Logout Button -->
        <?php
        // Assuming the user is logged in and you have the user ID or email in session
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            // Get the user ID from the session (adjust if you're using email instead)
            $userId = $_SESSION['user_id']; // Or $_SESSION['email'] if using email for login

            // Fetch the user's first name from the database
            include 'db_connect.php'; // Make sure the database connection is included
            $query = "SELECT fname FROM users WHERE ID = ?";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("i", $userId); // Assuming user_id is an integer
                $stmt->execute();
                $stmt->bind_result($fname);
                $stmt->fetch();
                $stmt->close();

                // Display the welcome message with the user's first name
                echo "<div class='welcome-message-container'>
                  <span class='welcome-message'>HI,</span>
                  <span class='user-name'>$fname</span>
                </div>";

                // Display the Logout button after the welcome message
                echo '<a href="logout.php" class="login-button">Logout</a>';
            }
        } else {
            // Show Login button if not logged in
            echo '<a href="Login.php" class="login-button">Login</a>';
        }
        ?>

        <!-- Mobile Navigation Toggle Button (Hamburger Menu) -->
        <div class="mobile-nav-toggle" onclick="toggleMobileNav()">&#9776;</div>
    </header>





    <div class="container">
        <!-- Payment Page Row -->
        <div class="row">
            <!-- Left side (form) -->
            <div class="col-lg-8 col-md-12">
                <h1>Checkout</h1>
                <p>All plans include advanced tools and features to boost your product. Choose the best plan to
                    fit your needs.</p>
                <form id="billing-form" method="POST" action="Payments.php">
                    <!-- Payment Methods Section -->
                    <div class="payment-methods">
                        <div class="form-checks form-check-inline">
                            <input class="form-check-input" type="radio" name="payment" value="credit" id="credit-card" checked onchange="togglePaymentMethod()">
                            <label class="form-check-label" for="credit-card">
                                <span class="custom-radio">
                                    <i class="fab fa-cc-visa"></i>
                                </span>
                                <span class="payment-label">Credit Card</span>
                            </label>
                        </div>
                        <div class="form-checks form-check-inline">
                            <input class="form-check-input" type="radio" name="payment" value="paypal" id="paypal" onchange="togglePaymentMethod()">
                            <label class="form-check-label" for="paypal">
                                <span class="custom-radio">
                                    <i class="fab fa-paypal"></i>
                                </span>
                                <span class="payment-label">Paypal</span>
                            </label>
                        </div>
                    </div>


                    <h2>Billing Details</h2>
                    <form id="billing-form" method="POST">
                        <!-- First Row: Email and Password -->
                        <div class="form-row">
                            <div class="form-group">
                                <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" placeholder="Password" required>
                            </div>
                        </div>


                        <!-- Credit Card Section -->
                        <div id="credit-card-info">
                            <h3>Credit Card Information</h3>
                            <div class="form-group">
                                <input type="text" class="form-control" name="cardNumber" placeholder="Card Number" maxlength="16" required>
                            </div>
                            <div class="form-group">
                                <select id="cardType" class="form-control" name="cardType" required>
                                    <option value="">Select Card Type</option>
                                    <option value="Visa">Visa</option>
                                    <option value="MasterCard">MasterCard</option>
                                </select>
                            </div>
                        </div>

            </div>

            <!-- Right Side (Order Summary) -->
            <div class="col-lg-4 col-md-12">
                <div class="order-summary">
                    <h2>Order Bundles</h2>

                    <div>
                        <label for="bundleSelect">Choose Your Bundle</label>
                        <select class="form-select" id="bundleSelect" name="planName" required>
                            <option value="" selected>Choose a bundle</option>
                            <?php
                            include 'db_connect.php';
                            $sql = "SELECT Name, Price FROM plan"; // Fetch Name and Price for the dropdown
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($row['Name']) . '">' . htmlspecialchars($row['Name']) . ' - $' . $row['Price'] . '/Year</option>';
                                }
                            } else {
                                echo '<option value="">No plans available</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Order Summary Section -->
                <button type="submit" class="btn btn-primary w-100">Proceed with Payment</button>
                <p class="terms">
                    By continuing, you accept our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. Please note that payments are non-refundable.
                </p>
                </form>
                <!-- Display Messages -->
                <?php if (!empty($message)): ?>
                    <div class="alert alert-info mt-4">
                        <?php echo nl2br(htmlspecialchars($message)); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </div>
    <footer>
        <footer>
            <div class="footer-content">
                <div class="footer-text">
                    &copy; <span id="year"></span> EBMS Platform<br>
                    Beirut, Lebanon | Verdun Street.<br>
                    Contact us: support@ebmsplatform.com
                </div>
                <div class="footer-social">
                    <a href="https://twitter.com" target="_blank" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="https://instagram.com" target="_blank" class="social-icon"><i
                            class="fab fa-instagram"></i></a>
                    <a href="https://facebook.com" target="_blank" class="social-icon"><i
                            class="fab fa-facebook"></i></a>
                    <a href="https://youtube.com" target="_blank" class="social-icon"><i class="fab fa-youtube"></i></a>
                </div>
                <div class="footer-links">
                    <a href="#">Terms of Service</a> |
                    <a href="#">Privacy Policy</a> |
                    <a href="ContactUs.php">Help Center</a>
                </div>
            </div>
        </footer>
    </footer>
    <script src="script.js"></script> <!-- Link to JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"
        integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD"
        crossorigin="anonymous"></script>
    <script src="/assets/js/theme.js" defer></script>
</body>

</html>
