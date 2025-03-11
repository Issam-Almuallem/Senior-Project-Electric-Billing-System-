<?php
$status = isset($_GET['status']) ? $_GET['status'] : 'error';
$message = isset($_GET['message']) ? $_GET['message'] : 'An unknown error occurred.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Response</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f4f4f4;
        }
        .response-container {
            display: inline-block;
            padding: 30px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="response-container">
        <h1 class="<?php echo htmlspecialchars($status); ?>">
            <?php echo htmlspecialchars($status === 'success' ? 'Success!' : 'Error!'); ?>
        </h1>
        <p><?php echo htmlspecialchars($message); ?></p>
        <a href="feedback.php" class="button">Go Back to Feedback</a>
    </div>
</body>
</html>
