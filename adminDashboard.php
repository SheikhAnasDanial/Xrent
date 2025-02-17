<?php
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'dbConnect.php';

$user_id = $_SESSION['user_id'];
$sql_admin_name = "SELECT adminName FROM admin WHERE adminID = '$user_id'";
$result_admin_name = $conn->query($sql_admin_name);
$adminName = $result_admin_name->fetch_assoc()['adminName'];

$adminNameLength = strlen($adminName);

// Fetch total rent count
$sql_total_rent_count = "SELECT COUNT(bookID) as totalRentCount FROM booking where bookStatus = 'Pending'";
$result_total_rent_count = $conn->query($sql_total_rent_count);
$totalRentCount = $result_total_rent_count->fetch_assoc()['totalRentCount'];

// Fetch grand total amount
$sql_grand_total_amount = "SELECT SUM(totalAmount) as grandTotalAmount FROM bill";
$result_grand_total_amount = $conn->query($sql_grand_total_amount);
$grandTotalAmount = $result_grand_total_amount->fetch_assoc()['grandTotalAmount'];

// Fetch number of feedbacks
$sql_feedback_count = "SELECT COUNT(fbID) as feedbackCount FROM feedback";
$result_feedback_count = $conn->query($sql_feedback_count);
$feedbackCount = $result_feedback_count->fetch_assoc()['feedbackCount'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .header {
            background: linear-gradient(90deg, #4B4B4B 0%, #0F0F0F 100%);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
            padding: 0 20px;
            z-index: 1000;
        }

        .logo {
            width: 120px;
            height: 65px;
            flex-shrink: 0;
            margin-left: 2rem;
        }

        .navbar {
            display: flex;
            align-items: center;
        }

        .dropdown {
            font-family: Poppins;
            position: relative;
            width: 120px;
            height: 45px;
            align-content: center;
            border: 1px solid #000;
            background: #FFF;
            margin-right: 3rem;
        }

        .dropdown p {
            margin-right: 2rem;
        }

        .dropdown img {
            margin-left: -1rem;
        }

        .dropdown a {
            color: black;
            display: flex;
            align-items: center;
            text-decoration: none;
            padding: 0 1rem;
            height: 100%;
            font-size: 18px;
            font-style: normal;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: calc(100% + 5px);
            left: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .admin-header {
            text-align: center;
            margin-top: 70px;
            margin-left: 0;
            margin-right: 0;
            color: #000;
            font-family: Poppins;
            font-size: 26px;
            font-style: normal;
            font-weight: 600;
            line-height: 1px;
            background-color: rgb(255, 255, 255);
            border: 1px solid black;
            padding: 10px;
            width: 100%;
            position: relative;
            z-index: 2;
        }

        .sidebar {
            margin-top: 60px;
            position: fixed;
            top: 70px;
            left: 0;
            width: 200px;
            background-color: #fff;
            padding: 20px 10px;
            height: 100%;
            border-right: 1px solid #000;
            z-index: 1;
        }

        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: #000;
            text-decoration: none;
            border-right: none;
            border-left: none;
            border-bottom: 1px solid black;
            text-align: left;
        }

        .sidebar a.active {
            background-color: #E1E1E1;
        }

        .sidebar a:first-child {
            border-top: none;
        }

        .sidebar a:hover {
            background-color: #f2eeee;
        }

        .main-content {
            margin-left: 220px;
            padding: 20px;
        }

        .card-wrapper {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: calc(33% - 20px);
            text-align: center;
        }

        .card h5 {
            margin: 0;
            font-size: 1.25rem;
            color: #333;
        }

        .card p {
            margin: 5px 0 0;
            font-size: 1.5rem;
            color: #666;
        }

        .charts-wrapper {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .chart-container {
            width: 48%;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script src="js/Chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const adminNameLength = <?php echo $adminNameLength; ?>;
            const dropdown = document.querySelector('.dropdown');
            dropdown.style.width = `${adminNameLength * 1 + 200}px`; 
        });
    </script>
</head>

<body>

    <header class="header">
        <img class="logo" src="image/logo.svg" alt="Logo XRENT">
        <nav class="navbar">
            <div class="dropdown">
                <a href="#">
                    <img class="iconprofile" src="image/icon profile.svg" alt="Icon Profile">
                    <p style="text-align: center;"><span><?php echo $adminName; ?></span></p>
                    <img class="iconarrow" src="image/icon arrow.svg" alt="Icon Arrow">
                </a>
                <div class="dropdown-content">
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="admin-header">
        <p>ADMIN PORTAL</p>
    </div>

    <div class="sidebar">
        <a href="bookingList.php">Manage Booking</a>
        <a href="carList.php">Cars</a>
        <a href="feedbackList.php">Feedback List</a>
    </div>

    <div class="main-content">

        <div class="card-wrapper">
            <div class="card">
                <h5>Pending Booking Count</h5>
                <p><?php echo $totalRentCount; ?></p>
            </div>
            <div class="card">
                <h5>Grand Total Amount</h5>
                <p>RM<?php echo number_format($grandTotalAmount, 2); ?></p>
            </div>
            <div class="card">
                <h5>Number of Feedbacks</h5>
                <p><?php echo $feedbackCount; ?></p>
            </div>
        </div>

        <div class="charts-wrapper">
            <div class="chart-container">
                <h3>Top 5 Cars by Rent Count</h3>
                <canvas id="barChart"></canvas>
            </div>

            <div class="chart-container">
                <h3>Rent Count by Car Brand</h3>
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const topCars = ["Viva", "Axia", "Persona", "Myvi", "Saga"];
        const topRents = [12, 19, 13, 15, 10];
        const categories = ["SUV", "Sedan", "4x4", "Small Car", "MPV"];
        const categoryRents = [10, 15, 2, 25, 5];

        const ctxBar = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: topCars,
                datasets: [{
                    label: 'Rent Count',
                    data: topRents,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const ctxPie = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: categories,
                datasets: [{
                    label: 'Rent Count by Category',
                    data: categoryRents,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>

</body>

</html>