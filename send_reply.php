<?php
// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure you adjust the path if you manually downloaded PHPMailer
require 'db_connect.php'; // Ensure the database connection is available

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['email'];
    $reply_message = $_POST['reply_message'];
    $subject = "Reply to Your feedback";
    $comment_id = $_POST['comment_id']; // Comment ID for updating status

    if (empty($to) || empty($reply_message) || empty($comment_id)) {
        header("Location: feedback_response.php?status=error&message=Missing email, message, or comment ID.");
        exit();
    }

    // Configure PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = ''; // Your Gmail address
        $mail->Password = '';    // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and Recipient
        $mail->setFrom('', 'Admin'); // Your email and name
        $mail->addAddress($to); // Recipient email

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = nl2br(htmlspecialchars($reply_message)); // Convert newlines to <br> tags for HTML content

        // Send Email
        $mail->send();

        // Update the reply status in the database
        $stmt = $conn->prepare("UPDATE comments SET Reply_Status = TRUE WHERE Com_ID = ?");
        $stmt->bind_param("i", $comment_id);
        if ($stmt->execute()) {
            header("Location: feedback_response.php?status=success&message=Reply sent successfully and status updated.");
        } else {
            header("Location: feedback_response.php?status=error&message=Reply sent but failed to update status.");
        }
        exit();
    } catch (Exception $e) {
        header("Location: feedback_response.php?status=error&message=Failed to send reply. {$mail->ErrorInfo}");
        exit();
    }
} else {
    header("Location: feedback_response.php?status=error&message=Invalid request.");
    exit();
}
