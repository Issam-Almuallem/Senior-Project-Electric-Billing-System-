<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch only active subscriptions and their user details
$users_sql = "
    SELECT 
        u.Fname, 
        u.Lname, 
        u.Address 
    FROM 
        users u
    INNER JOIN 
        subscriptions s 
    ON 
        u.ID = s.User_ID
    WHERE 
        s.End_D >= CURDATE() AND u.Address IS NOT NULL
";
$users_result = $conn->query($users_sql);

if (!$users_result) {
    die("Query failed: " . $conn->error);
}

$users = [];
while ($row = $users_result->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribed Users Map</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 2em;
        }

        header p {
            margin: 5px 0 0;
            font-size: 1.2em;
        }

        #map {
            width: 100%;
            height: 70vh;
            margin: 20px auto;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .button-container {
            text-align: center;
            margin: 20px 0;
        }

        .btn {
            background-color: #2a5298;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1em;
        }

        .btn:hover {
            background-color: #1e3c72;
        }

        @media (max-width: 768px) {
            header {
                padding: 15px;
            }

            header h1 {
                font-size: 1.8em;
            }

            #map {
                height: 60vh;
            }

            .btn {
                padding: 8px 16px;
                font-size: 0.9em;
            }
        }

        @media (max-width: 480px) {
            header {
                padding: 10px;
            }

            header h1 {
                font-size: 1.5em;
            }

            #map {
                height: 50vh;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Subscribed Users</h1>
        <p>Displaying active subscriptions on the map</p>
    </header>
    <div id="map"></div>
    <div class="button-container">
        <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
    </div>

    <script>
        // Initialize the map
        const map = L.map('map').setView([33.888630, 35.495480], 10); // Default to Beirut

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // User locations data from PHP
        const users = <?php echo json_encode($users); ?>;

        // Geocode user addresses and add markers
        users.forEach(user => {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(user.Address)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const { lat, lon } = data[0];

                        // Add a marker to the map
                        const marker = L.marker([lat, lon]).addTo(map);

                        // Add a popup for the marker
                        marker.bindPopup(`<strong>${user.Fname} ${user.Lname}</strong><br>${user.Address}`).openPopup();
                    } else {
                        console.error(`Geocode error for address "${user.Address}": No results found.`);
                    }
                })
                .catch(error => console.error('Geocode error:', error));
        });
    </script>
</body>
</html>
