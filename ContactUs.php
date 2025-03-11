<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

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




  <!-- Contact Section -->
  <section class="contact_section py-5">
    <div class="container">
      <div class="row">
        <!-- Image Box with Original Image -->
        <div class="col-md-6 mb-4">
          <div class="img-box">
            <img src="images/call-center-customer-service-tips-scaled.jpeg"
              class="img-fluid" alt="Customer Service Illustration">
          </div>
        </div>
        <!-- Form Container -->
        <div class="col-md-6 mx-auto">
          <div class="form_container">
            <div class="heading_container heading_center mb-4">
              <h2>Get Help With Your Electric Bill</h2>
              <p>Our customer support team is here to assist you with billing inquiries, payments, and more!</p>
            </div>
            <form action="ContactUs.php" method="POST">
              <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email" required />
              </div>
              <div class="mb-3">
                <textarea class="form-control" name="comment" placeholder="Please Add your Comments here" rows="3" required></textarea>
              </div>
              <div class="btn_box text-center">
                <button type="submit" class="btn btn-primary">Submit Request</button>
              </div>

            

            </form>
            <?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Capture and sanitize input
  $email = $conn->real_escape_string($_POST['email']);
  $comment = $conn->real_escape_string($_POST['comment']);

  // Get the user ID based on the email
  $userQuery = "SELECT ID FROM users WHERE Email = '$email'";
  $userResult = $conn->query($userQuery);

  if ($userResult->num_rows > 0) {
    $user = $userResult->fetch_assoc();
    $userId = $user['ID'];

    // Insert the comment into the comments table
    $commentQuery = "INSERT INTO comments (User_ID, Comment_Text) VALUES ('$userId', '$comment')";
    if ($conn->query($commentQuery) === TRUE) {
      echo '
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle"></i> <strong>Success!</strong> Your comment has been submitted successfully. Thank you for your feedback!
          <button type="button" class="closing-btn" data-bs-dismiss="alert" aria-label="Close">&times;</button>
        </div>
      ';
    } else {
      echo '
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fas fa-exclamation-circle"></i> <strong>Error!</strong> There was an issue submitting your comment. Please try again later.
          <button type="button" class="closing-btn" data-bs-dismiss="alert" aria-label="Close">&times;</button>
        </div>
      ';
    }
  } else {
    echo '
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> <strong>Warning!</strong> User with the given email does not exist.
        <button type="button" class="closing-btn" data-bs-dismiss="alert" aria-label="Close">&times;</button>
      </div>
    ';
  }
}
?>


          </div>
        </div>
   
    <!-- Testimonial Start -->
<div class="container-xxl py-5">
    <div class="container py-5 px-lg-5">
        <h1 class="text-center mb-5">Our Clients' Feedback</h1>
        <div class="slider-container">
            <!-- Testimonial Item 1 -->
            <div class="testimonial-item">
                <p class="fs-5"><i class="fa fa-quote-left fa-4x text-primary mt-n4 me-3"></i> "This electric billing system is incredibly efficient! It has saved us both time and money while improving billing accuracy." — Rami Farah, CEO of BeirutTech Solutions</p>
                <div class="d-flex align-items-center">
                    <img class="img-fluid flex-shrink-0 rounded-circle" src="images/testimonial-1.png" style="width: 65px; height: 65px;">
                    <div class="ps-4">
                        <h5 class="mb-1">Rami Farah</h5>
                        <span>CEO, BeirutTech Solutions</span>
                    </div>
                </div>
            </div>
            <!-- Testimonial Item 2 -->
            <div class="testimonial-item">
                <p class="fs-5"><i class="fa fa-quote-left fa-4x text-primary mt-n4 me-3"></i> "The platform's ease of use has allowed us to focus on other areas of the business while trusting the billing system to work smoothly!" — Leila Haddad, Founder of Cedars Energy</p>
                <div class="d-flex align-items-center">
                    <img class="img-fluid flex-shrink-0 rounded-circle" src="images/testimonial-2.jpg" style="width: 65px; height: 65px;">
                    <div class="ps-4">
                        <h5 class="mb-1">Leila Haddad</h5>
                        <span>Founder, Cedars Energy</span>
                    </div>
                </div>
            </div>
            <!-- Testimonial Item 3 -->
            <div class="testimonial-item">
                <p class="fs-5"><i class="fa fa-quote-left fa-4x text-primary mt-n4 me-3"></i> "We've seen significant improvements in our billing cycles. The system is intuitive and very reliable!" — Samir Kassem, Operations Manager at Green Lebanon</p>
                <div class="d-flex align-items-center">
                    <img class="img-fluid flex-shrink-0 rounded-circle" src="images/testimonial-3.jpg" style="width: 65px; height: 65px;">
                    <div class="ps-4">
                        <h5 class="mb-1">Samir Kassem</h5>
                        <span>Operations Manager, Green Lebanon</span>
                    </div>
                </div>
            </div>
            <!-- Testimonial Item 4 -->
            <div class="testimonial-item">
                <p class="fs-5"><i class="fa fa-quote-left fa-4x text-primary mt-n4 me-3"></i> "The billing automation features have transformed how we handle payments. We love how simple yet effective it is." — Ziad Abou-Rjeili, Finance Director at EcoPower Lebanon</p>
                <div class="d-flex align-items-center">
                    <img class="img-fluid flex-shrink-0 rounded-circle" src="images/testimonial-4.png" style="width: 65px; height: 65px;">
                    <div class="ps-4">
                        <h5 class="mb-1">Ziad Abou-Rjeili</h5>
                        <span>Finance Director, EcoPower Lebanon</span>
                    </div>
                </div>
            </div>
            <!-- Testimonial Item 5 -->
            <div class="testimonial-item">
                <p class="fs-5"><i class="fa fa-quote-left fa-4x text-primary mt-n4 me-3"></i> "The interface is user-friendly, and customer support is always responsive. We've definitely made the right choice!" — Noura El-Hage, CTO of LevantTech</p>
                <div class="d-flex align-items-center">
                    <img class="img-fluid flex-shrink-0 rounded-circle" src="images/testimonial-5.png" style="width: 65px; height: 65px;">
                    <div class="ps-4">
                        <h5 class="mb-1">Noura El-Hage</h5>
                        <span>CTO, LevantTech</span>
                    </div>
                </div>
            </div>
            <!-- Testimonial Item 6 -->
            <div class="testimonial-item">
                <p class="fs-5"><i class="fa fa-quote-left fa-4x text-primary mt-n4 me-3"></i> "This solution has changed the way we process payments. It’s a great tool for any growing business!" — Karim Jabbour, CEO of Payment Systems Lebanon</p>
                <div class="d-flex align-items-center">
                    <img class="img-fluid flex-shrink-0 rounded-circle" src="images/testimonial-6.jpg" style="width: 65px; height: 65px;">
                    <div class="ps-4">
                        <h5 class="mb-1">Karim Jabbour</h5>
                        <span>CEO, Payment Systems Lebanon</span>
                    </div>
                </div>
            </div>
            <!-- Testimonial Item 7 -->
            <div class="testimonial-item">
                <p class="fs-5"><i class="fa fa-quote-left fa-4x text-primary mt-n4 me-3"></i> "We’ve saved hours of manual work with the automated billing features. It's helped our team focus on higher-priority tasks!" — Maya El-Khoury, Marketing Director at FutureTech Lebanon</p>
                <div class="d-flex align-items-center">
                    <img class="img-fluid flex-shrink-0 rounded-circle" src="images/testimonial-7.jpg" style="width: 65px; height: 65px;">
                    <div class="ps-4">
                        <h5 class="mb-1">Maya El-Khoury</h5>
                        <span>Marketing Director, FutureTech Lebanon</span>
                    </div>
                </div>
            </div>
            <!-- Testimonial Item 8 -->
            <div class="testimonial-item">
                <p class="fs-5"><i class="fa fa-quote-left fa-4x text-primary mt-n4 me-3"></i> "A game changer! The system is incredibly efficient and has improved the speed and accuracy of our billing process." — Fadi Kassem, COO of SolarTech Lebanon</p>
                <div class="d-flex align-items-center">
                    <img class="img-fluid flex-shrink-0 rounded-circle" src="images/testimonial-8.png" style="width: 65px; height: 65px;">
                    <div class="ps-4">
                        <h5 class="mb-1">Fadi Kassem</h5>
                        <span>COO, SolarTech Lebanon</span>
                    </div>
                </div>
            </div>
            <!-- Testimonial Item 9 -->
            <div class="testimonial-item">
                <p class="fs-5"><i class="fa fa-quote-left fa-4x text-primary mt-n4 me-3"></i> "We are extremely satisfied with how this system integrates with our accounting software. It's made billing easier and more accurate." — Faris Sayegh, Account Manager at BlueSky Lebanon</p>
                <div class="d-flex align-items-center">
                    <img class="img-fluid flex-shrink-0 rounded-circle" src="images/testimonial-9.png" style="width: 65px; height: 65px;">
                    <div class="ps-4">
                        <h5 class="mb-1">Faris Sayegh</h5>
                        <span>Account Manager, BlueSky Lebanon</span>
                    </div>
                </div>
            </div>
            <!-- Testimonial Item 10 -->
            <div class="testimonial-item">
                <p class="fs-5"><i class="fa fa-quote-left fa-4x text-primary mt-n4 me-3"></i> "This tool has been a huge time-saver for our team. The automation has freed up our resources to focus on growing the business." — Rania Tohme, Project Manager at EcoBuild Lebanon</p>
                <div class="d-flex align-items-center">
                    <img class="img-fluid flex-shrink-0 rounded-circle" src="images/testimonial-10.jpg" style="width: 65px; height: 65px;">
                    <div class="ps-4">
                        <h5 class="mb-1">Rania Tohme</h5>
                        <span>Project Manager, EcoBuild Lebanon</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Testimonial End -->
</section>
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
                <a href="https://instagram.com" target="_blank" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="https://facebook.com" target="_blank" class="social-icon"><i class="fab fa-facebook"></i></a>
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