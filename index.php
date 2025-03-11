<?php
// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'db_connect.php';
session_start();

// Database Connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "ebms"; // Replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current date and calculate reminder date
$currentDate = date('Y-m-d');
$reminderDate = date('Y-m-d', strtotime('+7 days')); // 7 days before expiration

// Load the list of sent emails from a file
$sentEmailsFile = 'sent_emails.json';
$sentEmailsData = [];

if (file_exists($sentEmailsFile)) {
    $sentEmailsData = json_decode(file_get_contents($sentEmailsFile), true);
}

// Check if the stored date is different from the current date
if (!isset($sentEmailsData['date']) || $sentEmailsData['date'] !== $currentDate) {
    // Clear the sent emails array if the date has changed
    $sentEmailsData = [
        'date' => $currentDate,
        'emails' => []
    ];
}

// Get the list of already sent emails
$sentEmails = $sentEmailsData['emails'];

// Query to get user emails and names for subscriptions nearing expiration
$sql = "
    SELECT users.Email, users.Fname, subscriptions.End_D 
    FROM subscriptions 
    INNER JOIN users ON subscriptions.User_ID = users.ID
    WHERE subscriptions.End_D = '$reminderDate'
";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = $row['Email'];
        $firstName = $row['Fname'];
        $endDate = $row['End_D'];

        // Check if the email has already been sent
        if (in_array($email, $sentEmails)) {
            continue; // Skip this email
        }

        // Add the email to the sent emails list
        $sentEmails[] = $email;

        // Configure PHPMailer
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '12133482@students.liu.edu.lb'; // Your Gmail address
            $mail->Password = 'yuop vydl afuq suti'; // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email Content
            $mail->setFrom('12133482@students.liu.edu.lb', 'Admin');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Subscription Expiration Reminder";
            $mail->Body = "
                <p>Dear $firstName,</p>
                <p>Your subscription will expire on <b>$endDate</b>. Please renew your subscription to continue enjoying our services.</p>
                <p>Thank you!</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            // Log error or handle exception
        }
    }
}

// Save the updated list of sent emails and the current date back to the file
$sentEmailsData['emails'] = $sentEmails;
file_put_contents($sentEmailsFile, json_encode($sentEmailsData));

$conn->close();
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
    <title>EBMS Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css"
        integrity="sha384-b6lVK+yci+bfDmaY1u0zE8YYJt0TZxLEAFyYSLHId4xoVvsrQu3INevFKo+Xir8e" crossorigin="anonymous" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" rel="stylesheet">
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


    <div class="slider">
        <div class="slides" id="slides">
            <div class="slide" id="home">
                <h2>Welcome to EBMS Platform</h2>
                <p>Your one-stop solution for managing your electric bills efficiently.</p>
                <img src="images/imgHomes.jpg">
                <a href="index.php" class="button">Learn More</a>
            </div>
            <div class="slide" id="services">
                <h2>Our Services</h2>
                <p>Explore our range of services tailored to help you manage your electricity consumption.</p>
                <img src="images/ourservices.jpg" alt="Our Services">
                <a href="OurServices.php" class="button">Learn More</a>
            </div>
            <div class="slide" id="about">
                <h2>About Us</h2>
                <p>Learn about our mission and how we aim to empower our users.</p>
                <img src="images/About.jpg" alt="About Us">
                <a href="AboutUS.php" class="button">Learn More</a>
            </div>
            <div class="slide" id="contact">
                <h2>Contact Us</h2>
                <p>Get in touch with us for any inquiries or support.</p>
                <img src="images/Screenshot 2024-11-05 202652.png" alt="Contact Us">
                <a href="ContactUs.php" class="button">Learn More</a>
            </div>

        </div>
    </div>

    <!-- Feature Section Start -->
    <div class="feature-section">
        <div class="container feature">
            <div class="row">
                <!-- Feature Text Section -->
                <div class="col-lg-6 feature-text">
                    <div class="content">
                        <p class="feature-label">Key Features</p>
                        <h1>Why Choose Our Electric Billing System</h1>
                        <p class="feature-description">
                            Our Electric Billing System provides a user-friendly, reliable, and secure platform to
                            manage your electricity billing. With real-time data, automated calculations, and easy
                            access to billing history, we make managing your energy costs simpler and more
                            transparent.
                        </p>
                        <div class="feature-list">
                            <div class="feature-item">
                                <div class="icon">
                                    <i class="fa fa-bolt"></i>
                                </div>
                                <div class="text">
                                    <p class="label">Accurate Billing</p>
                                    <h5>Real-time Calculations</h5>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="icon">
                                    <i class="fa fa-check-circle"></i>
                                </div>
                                <div class="text">
                                    <p class="label">Secure Payments</p>
                                    <h5>Multiple Payment Methods</h5>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="icon">
                                    <i class="fa fa-chart-line"></i>
                                </div>
                                <div class="text">
                                    <p class="label">Cost Efficiency</p>
                                    <h5>Track Usage & Reduce Costs</h5>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="icon">
                                    <i class="fa fa-headset"></i>
                                </div>
                                <div class="text">
                                    <p class="label">24/7 Support</p>
                                    <h5>Assistance Anytime</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Section -->
                <div class="col-lg-6 feature-image">
                    <div class="image-wrapper">
                        <img src="images/why chooseus.jpg" alt="Electric Billing System">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Feature Section End -->

    <h2 id=""> FUTURE PROOF YOUR SOLUTIONS AND DELIVER MORE</h2>
    <br>
    <p id="p1">Elevate your compliance, operational, treasury and customer experience capabilities with
        game-changing
        technology.
    </p>

    <div class="container">
        <div class="row">
            <div class="item">
                <img src="images/icon-highly-configurable.png" alt="Image 1">
                <h3>Highly Configurable</h3>
                <p>Our Electric Bill Management System (EBMS) is designed for flexibility and adaptability. With automated billing, flexible payment options, and customizable settings, our system can cater to any building's unique requirements—no additional development needed. Adjust the platform to fit your exact needs with ease.</p>
            </div>
            <div class="item">
                <img src="images/icon-flexible.png" alt="Image 2">
                <h3>Flexible Integration</h3>
                <p>Easily integrate our EBMS with existing platforms like CRM, ERP, and accounting systems. Through seamless API connections, our system allows property managers and residents to have a customizable and unified billing experience, with full control over billing cycles, payment methods, and notifications.</p>
            </div>
            <div class="item">
                <img src="images/compliant.png" alt="Image 3">
                <h3>Compliant and Secure</h3>
                <p>Our platform meets the highest industry standards for data protection. We ensure that all electric billing transactions are secure, with end-to-end encryption, multi-factor authentication, and compliance with local regulatory requirements. Your billing data is safe with us at all times.</p>
            </div>
        </div>
        <div class="row">
            <div class="item">
                <img src="images/Secureandreliable.png" alt="Image 4">
                <h3>Secure and Reliable</h3>
                <p>Reliability is at the core of our Electric Bill Management System. With state-of-the-art encryption, real-time transaction monitoring, and guaranteed uptime, our platform ensures that every payment is processed securely and accurately, with no service interruptions.</p>
            </div>
            <div class="item">
                <img src="images/Widelyintegrated.png" alt="Image 5">
                <h3>Widely Integrated</h3>
                <p>Our system integrates with over 100 major platforms, including utilities, accounting, and payment systems. This integration ensures smooth synchronization of billing data across all your operational tools, making it easier to manage electric billing and payment workflows for both property managers and residents.</p>
            </div>
            <div class="item">
                <img src="images/Architectedforthefuture.png" alt="Image 6">
                <h3>Architected for the Future</h3>
                <p>Our scalable and future-proof platform is built to grow with your needs. With a single unified code base, our system supports continuous updates and enhancements, allowing it to adapt to emerging technologies, new regulations, and evolving user requirements in electric billing and payment processing.</p>
            </div>
        </div>

        <h2 id="d1">
            Trusted by Millions.</h2>
    </div>
    <footer>
        <footer>
            <div class="footer-content">
                <div class="footer-text">
                    &copy; <span id="year"></span> EBMS Platform<br>
                    <i class="fas fa-map-pin icon"></i> Beirut, Lebanon | Verdun Street.<br>
                    <i class="fas fa-inbox"></i>
                    Contact us: support@ebmsplatform.com<br>
                    <i class="fas fa-phone icon"></i> +012334567
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
</body>

</html>