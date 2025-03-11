<?php
session_start();
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



  <!-- About Section -->
  <section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container heading_center">
        <h2>Learn More About <span>Us</span></h2>
        <p>At EBMS, we're committed to reshaping the future of utility bill management with innovative digital solutions.</p>
      </div>
      <div class="row">
        <div class="col-md-7">
          <div class="detail-box">
            <h3>Our Mission at EBMS</h3>
            <p>At EBMS, our goal is simple – to streamline the billing process, offering a seamless, paperless experience for residents and property managers. Our platform leverages the latest in secure technology to allow users to track, manage, and pay their bills with ease and transparency.</p>
            <p>Founded in 2020 by a passionate team of technology experts, we've quickly grown into a leading provider of electric bill management services. Our commitment to sustainability drives us to eliminate paper waste and deliver a reliable digital service to our users.</p>
          </div>
        </div>
      </div>


      <!-- team section -->
      <section class="team_section layout_padding">
        <div class="container-fluid">
          <div class="heading_container heading_center">
            <h2 class="">
              Our <span> Team</span>
            </h2>
          </div>

          <div class="team_container">
            <div class="row">
              <div class="col-lg-3 col-sm-6">
                <div class="box ">
                  <div class="img-box">
                    <img src="images/iconimg.avif" class="img1" alt="">
                  </div>
                  <div class="detail-box">
                    <h5>
                      Issam Al-Muallem
                    </h5>

                    Admin
                    <p><a href="mailto:issammuallem@gmail.com">issammuallem@gmail.com</a></p>

                  </div>
                  <div class="social_box">
                    <a href="#">
                      <i class="fab fa-facebook" aria-hidden="true"></i>
                    </a>
                    <a href="#">
                      <i class="fab fa-twitter" aria-hidden="true"></i>
                    </a>
                    <a href="#">
                      <i class="fab fa-linkedin" aria-hidden="true"></i>
                    </a>
                    <a href="#">
                      <i class="fab fa-instagram" aria-hidden="true"></i>
                    </a>
                    <a href="#">
                      <i class="fab fa-youtube-play" aria-hidden="true"></i>
                    </a>
                  </div>
                </div>
              </div>

              <div class="col-lg-3 col-sm-6">
                <div class="box ">
                  <div class="img-box">
                    <img src="images/iconimgsg.png" class="img1" alt="">
                  </div>
                  <div class="detail-box">
                    <h5>
                      Maria Al-Ahmad
                    </h5>

                    Admin
                    <p><a href="mailto:mariaalahmad@gmail.com">mariaalahmad@gmail.com</a></p>

                  </div>
                  <div class="social_box">
                    <a href="#">
                      <i class="fab fa-facebook" aria-hidden="true"></i>
                    </a>
                    <a href="#">
                      <i class="fab fa-twitter" aria-hidden="true"></i>
                    </a>
                    <a href="#">
                      <i class="fab fa-linkedin" aria-hidden="true"></i>
                    </a>
                    <a href="#">
                      <i class="fab fa-instagram" aria-hidden="true"></i>
                    </a>
                    <a href="#">
                      <i class="fab fa-youtube-play" aria-hidden="true"></i>
                    </a>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
    </div>
  </section>
  <!-- end team section -->

  <div>
    <h1>Our Location Map</h1>
    <div class="map-container">
      <iframe
        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDxjADa0fb_T2zU8_wWrm5hJuzmiFJrDcI&q=Verdun+Street,Beirut,Lebanon"
        allowfullscreen>
      </iframe>
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