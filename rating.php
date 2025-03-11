<?php
session_start(); // Start the session

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

include 'db_connect.php'; // Include DB connection

$message = ""; // Initialize a message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $user_id = $_SESSION['user_id']; // Retrieve User ID from session

    // Validate the rating
    if ($rating < 1 || $rating > 5) {
        $message = "Invalid rating value. Please select a value between 1 and 5.";
    } else {
        // Check if the user has already submitted a rating
        $checkQuery = "SELECT COUNT(*) AS count FROM ratings WHERE User_ID = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $message = "You have already submitted a rating. Thank you!";
        } else {
            // Insert the rating into the `ratings` table
            $stmt = $conn->prepare("INSERT INTO ratings (Rating_Value, User_ID) VALUES (?, ?)");
            $stmt->bind_param("ii", $rating, $user_id);

            if ($stmt->execute()) {
                $message = "Thank you for your feedback!";
            } else {
                $message = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Your Experience</title>
    <style>
        /* General Body Styling */
        body {
            font-family: 'Orbitron', sans-serif;
            text-align: center;
            background: linear-gradient(135deg, #2b2e4a, #007BFF, #fdc830);
            background-size: 400% 400%;
            animation: backgroundAnimation 15s ease infinite;
            margin: 0;
            padding: 0;
            color: white;
        }

        @keyframes backgroundAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        h1 {
            margin-top: 30px;
            font-size: 2.5rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        #rating-form {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            color: #333;
        }

        h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .stars {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
        }

        .star {
            font-size: 50px;
            color: #ccc;
            cursor: pointer;
            transition: color 0.3s, transform 0.3s;
        }

        .star:hover {
            color: #FFD700;
            transform: scale(1.2);
        }

        .star.selected {
            color: #FFD700;
            transform: scale(1.3);
        }

        button {
            padding: 12px 25px;
            font-size: 18px;
            background: linear-gradient(45deg, #007BFF, #fdc830);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            transform: scale(1.05);
            background: linear-gradient(45deg, #fdc830, #007BFF);
        }

        .message {
            margin-top: 20px;
            font-size: 1.1rem;
        }

        .message.error {
            color: red;
        }

        .back-btn {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            #rating-form {
                padding: 20px;
            }

            h2 {
                font-size: 1.5rem;
            }

            .star {
                font-size: 40px;
            }

            button {
                padding: 10px 20px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.8rem;
            }

            #rating-form {
                padding: 15px;
            }

            h2 {
                font-size: 1.3rem;
            }

            .star {
                font-size: 35px;
            }

            button {
                padding: 8px 18px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <h1>Rate Us!</h1>
    <a href="index.php" class="back-btn">Back to Home Page</a>

    <div id="rating-form">
        <h2>Rate Your Experience</h2>
        <p>Your feedback helps us improve!</p>
        <form method="POST" action="">
            <div class="stars">
                <span class="star" data-value="1">&#9733;</span>
                <span class="star" data-value="2">&#9733;</span>
                <span class="star" data-value="3">&#9733;</span>
                <span class="star" data-value="4">&#9733;</span>
                <span class="star" data-value="5">&#9733;</span>
            </div>
            <input type="hidden" name="rating" id="rating-input" value="">
            <button type="submit">Submit Feedback</button>
        </form>
        <?php if (!empty($message)): ?>
            <p class="message <?php echo strpos($message, 'Error') !== false ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
    </div>

    <script>
        // Handle star rating
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating-input');

        stars.forEach(star => {
            star.addEventListener('click', () => {
                const selectedRating = star.getAttribute('data-value');
                ratingInput.value = selectedRating;

                // Highlight selected stars
                stars.forEach(s => s.classList.remove('selected'));
                for (let i = 0; i < selectedRating; i++) {
                    stars[i].classList.add('selected');
                }
            });
        });
    </script>
</body>
</html>
