<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch comments with user emails and reply status
$comments_sql = "
    SELECT 
        comments.Com_ID, 
        comments.Comment_Text, 
        comments.Time, 
        users.Email, 
        users.Fname, 
        users.Lname, 
        comments.Reply_Status -- Include Reply_Status here
    FROM comments
    INNER JOIN users ON comments.User_ID = users.id
";
$comments_result = $conn->query($comments_sql);

// Fetch ratings with user emails
$ratings_sql = "
    SELECT 
        ratings.Rating_ID, 
        ratings.Rating_Value, 
        users.Email, 
        users.Fname, 
        users.Lname
    FROM ratings
    INNER JOIN users ON ratings.User_ID = users.id
";
$ratings_result = $conn->query($ratings_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments & Ratings Feedback</title>
    <style>
   /* General Styles */
body {
    font-family: Arial, sans-serif;
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
    font-size: 2em;
}

.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 40px;
    overflow-x: auto;
}

table th, table td {
    text-align: left;
    padding: 10px;
    border: 1px solid #ddd;
    font-size: 14px;
}

table th {
    background: #f4f4f4;
}

table tr:hover {
    background: #f1f7fc;
}

/* Reply form styling */
form {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
}

textarea {
    width: 97%;
    max-width: 100%;
    height: 60px;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: none;
    font-size: 14px;
}

.btn-reply {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    background-color: #28a745;
    color: #fff;
    font-size: 14px;
    align-self: flex-start;
}

.btn-reply:hover {
    background-color: #218838;
}

.btn-back {
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    background-color: #28a745;
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    cursor: pointer;
}

.btn-back:hover {
    background-color: #218838;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 10px;
        margin: 10px;
    }

    header h1 {
        font-size: 1.5em;
    }

    table th, table td {
        font-size: 12px;
        padding: 8px;
    }

    textarea {
        font-size: 12px;
        height: 50px;
    }

    .btn-reply {
        font-size: 12px;
        padding: 5px 10px;
    }

    .btn-back {
        font-size: 14px;
        padding: 8px 12px;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 5px;
        margin: 5px;
    }

    header h1 {
        font-size: 1.2em;
    }

    table th, table td {
        font-size: 10px;
        padding: 6px;
    }

    textarea {
        font-size: 10px;
        height: 40px;
        width: 40px;
    }

    .btn-reply {
        font-size: 10px;
        padding: 4px 8px;
    }

    .btn-back {
        font-size: 12px;
        padding: 6px 10px;
    }
}

    </style>
</head>
<body>
    <header>
        <h1>Comments & Ratings Feedback</h1>
        <p>View comments, ratings, and reply via email</p>
    </header>
    <div class="container">
        <a href="admin_dashboard.php" class="btn-back">Back to Main Dashboard</a>
        <!-- Comments Section -->
        <h2>Comments</h2>
        <table>
            <thead>
                <tr>
                    <th>Comment</th>
                    <th>Time</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Reply</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($comments_result->num_rows > 0): ?>
                    <?php while ($row = $comments_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Comment_Text']); ?></td>
                            <td><?php echo htmlspecialchars($row['Time']); ?></td>
                            <td><?php echo htmlspecialchars($row['Fname'] . ' ' . $row['Lname']); ?></td>
                            <td><?php echo htmlspecialchars($row['Email']); ?></td>
                            <td>
                                <?php if (isset($row['Reply_Status']) && $row['Reply_Status']): ?>
                                    Replied
                                <?php else: ?>
                                    <form action="send_reply.php" method="POST">
                                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['Email']); ?>">
                                        <input type="hidden" name="comment_id" value="<?php echo $row['Com_ID']; ?>">
                                        <textarea name="reply_message" placeholder="Write your reply..." required></textarea>
                                        <button type="submit" class="btn-reply">Send Reply</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td><?php echo isset($row['Reply_Status']) && $row['Reply_Status'] ? 'Replied' : 'Not Replied'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No comments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Ratings Section -->
        <h2>Ratings</h2>
        <table>
            <thead>
                <tr>
                    <th>Rating</th>
                    <th>User Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ratings_result->num_rows > 0): ?>
                    <?php while ($row = $ratings_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Rating_Value']); ?></td>
                            <td><?php echo htmlspecialchars($row['Fname'] . ' ' . $row['Lname']); ?></td>
                            <td><?php echo htmlspecialchars($row['Email']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No ratings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
