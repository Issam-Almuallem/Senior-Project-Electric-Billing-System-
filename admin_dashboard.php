<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'admin') {
    // Redirect to login page if not logged in or not an admin
    header("Location: login.php");
    exit();
}

// Admin is logged in, show the dashboard
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
/* General Styles */
body {
    font-family: 'Arial', sans-serif;
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
    font-size: 2.5em;
    animation: slideIn 1s ease-out;
}

header p {
    font-size: 1.2em;
    margin-top: 10px;
    opacity: 0.8;
}

/* Navigation Bar */
nav {
    display: flex;
    justify-content: center;
    background: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 15px 0;
    flex-wrap: wrap;
    position: relative;
}

nav a {
    margin: 5px 15px;
    text-decoration: none;
    color: #333;
    font-size: 16px;
    position: relative;
    padding-bottom: 5px;
    font-weight: bold;
}

nav a:hover {
    color: #007BFF;
}

nav a::after {
    content: '';
    display: block;
    height: 2px;
    width: 0;
    background: #007BFF;
    transition: width 0.3s ease-in-out;
}

nav a:hover::after {
    width: 100%;
}

/* Section */
section {
    margin: 20px auto;
    max-width: 1200px;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    animation: fadeIn 1.5s ease;
}

/* Dashboard Sections */
.dashboard-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px; /* Adjust gap for better spacing */
    padding: 20px;
}

.dashboard-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.dashboard-card i {
    font-size: 2.5em;
    color: #007BFF;
    margin-bottom: 10px;
}

.dashboard-card h3 {
    margin: 10px 0;
    font-size: 1.2em;
    color: #333;
}

.dashboard-card p {
    color: #666;
    font-size: 0.95em;
}

/* Footer */
footer {
    text-align: center;
    padding: 20px 0;
    background-color: #1e3c72;
    color: #fff;
    position: fixed;
    width: 100%;
    bottom: 0;
    box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.1);
}

/* Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Responsive Adjustments */
@media (max-width: 1024px) {
    .dashboard-links {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .dashboard-card {
        padding: 15px;
    }

    .dashboard-card i {
        font-size: 2.2em;
    }

    .dashboard-card h3 {
        font-size: 1.1em;
    }

    .dashboard-card p {
        font-size: 0.9em;
    }
}

@media (max-width: 768px) {
    .dashboard-links {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .dashboard-card {
        padding: 15px;
    }

    .dashboard-card i {
        font-size: 2em;
    }

    .dashboard-card h3 {
        font-size: 1em;
    }

    .dashboard-card p {
        font-size: 0.85em;
    }
}

@media (max-width: 480px) {
    .dashboard-links {
        gap: 10px;
    }

    .dashboard-card {
        padding: 10px;
    }

    .dashboard-card i {
        font-size: 1.8em;
    }

    .dashboard-card h3 {
        font-size: 0.9em;
    }

    .dashboard-card p {
        font-size: 0.8em;
    }
}

/* Hamburger Menu */
.menu-toggle {
    display: none;
    flex-direction: column;
    cursor: pointer;
    position: absolute;
    right: 20px;
    top: -1px;
    z-index: 10;
}

.bar {
    height: 3px;
    width: 25px;
    background-color: #333;
    margin: 4px 0;
    transition: 0.3s;
}

.nav-links {
    list-style: none;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
    padding: 0;
}

.nav-links li {
    margin: 0 10px;
}

.nav-links a {
    text-decoration: none;
    color: #333;
    font-size: 16px;
    font-weight: bold;
    position: relative;
    padding-bottom: 5px;
}

.nav-links a:hover {
    color: #007BFF;
}

.nav-links a::after {
    content: '';
    display: block;
    height: 2px;
    width: 0;
    background: #007BFF;
    transition: width 0.3s ease-in-out;
}

.nav-links a:hover::after {
    width: 100%;
}

/* Responsive Menu */
@media (max-width: 768px) {
    .menu-toggle {
        display: flex;
    }

    .nav-links {
        display: none;
        flex-direction: column;
        align-items: center;
        position: absolute;
        top: 100%;
        right: 0;
        background-color: #fff;
        width: 100%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .nav-links.active {
        display: flex;
    }

    .nav-links li {
        margin: 10px 0;
    }
}

    </style>
</head>
<body>
<header>
    <h1>Admin Dashboard</h1>
    <p>Managing Your Electric Billing Platform Efficiently</p>
</header>
<nav>
    <div class="menu-toggle" id="mobileMenu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>
    <ul class="nav-links">
        <li><a href="subs.php"><i class="fas fa-users icon"></i> Subscribers</a></li>
        <li><a href="Plans.php"><i class="fas fa-list icon"></i> Plans</a></li>
        <li><a href="Bill.php"><i class="fas fa-file-invoice-dollar icon"></i> Billing</a></li>
        <li><a href="track.php"><i class="fas fa-map-marker-alt icon"></i> Tracking</a></li>
        <li><a href="stats.php"><i class="fas fa-chart-bar icon"></i> Statistics</a></li>
        <li><a href="feedback.php"><i class="fas fa-comments icon"></i> Feedback</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt icon"></i> Logout</a></li>
    </ul>
</nav>

    <section>
        <div class="dashboard-links">
            <div class="dashboard-card">
                <i class="fas fa-users"></i>
                <h3>Subscribers</h3>
                <p>View and manage all subscribers.</p>
            </div>
            <div class="dashboard-card">
                <i class="fas fa-list"></i>
                <h3>Plans</h3>
                <p>Customize and manage subscription plans.</p>
            </div>
            <div class="dashboard-card">
                <i class="fas fa-file-invoice-dollar"></i>
                <h3>Billing</h3>
                <p>Track and process all bills.</p>
            </div>
            <div class="dashboard-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Tracking</h3>
                <p>Monitor service areas and more.</p>
            </div>
            <div class="dashboard-card">
                <i class="fas fa-chart-bar"></i>
                <h3>Statistics</h3>
                <p>Analyze usage and financial data.</p>
            </div>
            <div class="dashboard-card">
                <i class="fas fa-comments"></i>
                <h3>Feedback</h3>
                <p>Review and respond to customer feedback.</p>
            </div>
        </div>
    </section>
    <footer>
        &copy; <?php echo date("Y"); ?> Electric Billing Admin Panel. 
    </footer>
    <script>
    // JavaScript to toggle the mobile menu
    const mobileMenu = document.getElementById('mobileMenu');
    const navLinks = document.querySelector('.nav-links');

    mobileMenu.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        mobileMenu.classList.toggle('active');
    });
</script>

</body>
</html>
