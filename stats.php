<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumption Dashboard</title>
    <link rel="icon" href="https://img.icons8.com/color/48/000000/lightning-bolt.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
            padding: 20px 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        header h1 {
            margin: 0;
            font-size: 1.8em;
        }
        header p {
            margin: 5px 0 0;
            font-size: 0.9em;
        }
        .container {
            max-width: 95%;
            margin: 20px auto;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        footer {
            text-align: center;
            margin: 20px 0;
            font-size: 14px;
            color: #888;
        }
        canvas {
            display: block;
            margin: 0 auto;
            width: 100%;
            height: auto;
            max-height: 500px;
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
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #218838;
        }
        @media (max-width: 768px) {
            header h1 {
                font-size: 1.5em;
            }
            header p {
                font-size: 0.8em;
            }
            .btn-back {
                font-size: 14px;
            }
        }
        @media (max-width: 480px) {
            header {
                padding: 15px 10px;
            }
            header h1 {
                font-size: 1.3em;
            }
            header p {
                font-size: 0.7em;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Consumption Dashboard</h1>
        <p>Visualize users' consumption insights</p>
    </header>
    <div class="container">
        <a href="admin_dashboard.php" class="btn-back">Back to Main Dashboard</a>
        <canvas id="consumptionChart"></canvas>
    </div>
   <script>
        const ctx = document.getElementById('consumptionChart').getContext('2d');
        let consumptionChart;

        function fetchConsumptionData() {
            $.ajax({
                url: 'fetch_consumption.php',
                method: 'GET',
                success: function(response) {
                    const data = JSON.parse(response);

                    if (data.length === 0) {
                        console.warn("No data found.");
                        return;
                    }

                    const usernames = data.map(item => item.username);
                    const consumptionLevels = data.map(item => item.total_consumption);

                    if (consumptionChart) {
                        consumptionChart.data.labels = usernames;
                        consumptionChart.data.datasets[0].data = consumptionLevels;
                        consumptionChart.update();
                    } else {
                        consumptionChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: usernames,
                                datasets: [{
                                    label: 'Total Consumption (kWh)',
                                    data: consumptionLevels,
                                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 2,
                                    hoverBackgroundColor: 'rgba(54, 162, 235, 0.8)'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: {
                                            color: '#333',
                                            font: {
                                                size: 16
                                            }
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return `${context.raw} kWh`;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Username',
                                            color: '#333',
                                            font: {
                                                size: 14,
                                                weight: 'bold'
                                            }
                                        },
                                        ticks: {
                                            color: '#555'
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: 'Consumption (kWh)',
                                            color: '#333',
                                            font: {
                                                size: 14,
                                                weight: 'bold'
                                            }
                                        },
                                        ticks: {
                                            color: '#555',
                                            callback: function(value) {
                                                return `${value} kWh`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                },
                error: function(error) {
                    console.error("AJAX error:", error);
                }
            });
        }

        fetchConsumptionData();
        setInterval(fetchConsumptionData, 5000);
    </script>
</body>
</html>
