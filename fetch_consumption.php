<?php
include 'db_connect.php';

// Query to join users and consumptionrecords tables
$sql = "
    SELECT CONCAT(u.Fname, ' ', u.Lname) AS username, SUM(c.Consumption) AS total_consumption
    FROM users u
    JOIN consumptionrecords c ON u.ID = c.User_ID
    GROUP BY u.ID
    ORDER BY total_consumption DESC
";
$result = $conn->query($sql);

// Check if the query returned any rows
if ($result && $result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo json_encode([]); // Return an empty array if no data is found
}

// Close the connection
$conn->close();
?>
