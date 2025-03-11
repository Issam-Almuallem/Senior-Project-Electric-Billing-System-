<?php
session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validate the token and expiry in the session
    if (isset($_SESSION['reset_token']) && $_SESSION['reset_token'] === $token) {
        if (time() < $_SESSION['reset_expiry']) {
            // Token is valid and not expired
            echo "Password reset has been approved. You can now proceed to reset your password.";
            
            // Optionally, you can unset the token here to prevent reuse
            unset($_SESSION['reset_token']);
            unset($_SESSION['reset_expiry']);
        } else {
            echo "The reset link has expired.";
        }
    } else {
        echo "Invalid reset link.";
    }
} else {
    echo "No token provided.";
}
?>
