<?php

session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once 'dbConnect.php';

$user_id = $_SESSION['user_id'];
$sql_admin_name = "SELECT adminName FROM admin WHERE adminID = '$user_id'";
$result_admin_name = $conn->query($sql_admin_name);
$adminName = $result_admin_name->fetch_assoc()['adminName'];

$adminNameLength = strlen($adminName);

// Function to update booking status
function updateBookingStatus($bookID, $status)
{
    $dbCon = new mysqli("localhost", "root", "", "xrent");
    $sql = "UPDATE booking SET bookStatus = '$status' WHERE bookID = '$bookID'";
    mysqli_query($dbCon, $sql);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirm'])) {
        updateBookingStatus($_POST['bookID'], 'Confirmed');
    } elseif (isset($_POST['reject'])) {
        updateBookingStatus($_POST['bookID'], 'Rejected');
    }
    header("Location: bookingList.php");
    exit(); // Ensure no further code execution after redirect
}

// Retrieve booking details
if (isset($_GET['bookID'])) {
    $bookID = $_GET['bookID'];
    $sql = "SELECT b.bookID, b.bookStatus, b.bookDate, b.startDate, b.startTime, b.endDate, b.endTime, 
                   b.totalHour, b.receiptProof, b.adminID, b.custID, b.carID, 
                   c.carName, cust.custName, cust.drivingLicense
            FROM booking b
            LEFT JOIN car c ON b.carID = c.carID
            LEFT JOIN customer cust ON b.custID = cust.custID
            WHERE b.bookID = '$bookID'";

    $dbCon = new mysqli("localhost", "root", "", "xrent");
    $result = mysqli_query($dbCon, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $booking = mysqli_fetch_assoc($result);
        $status = $booking['bookStatus']; // Retrieve status
    } else {
        echo "Booking not found.";
        exit;
    }
} else {
    echo "Booking ID not specified.";
    exit;
}

// Close database connection
mysqli_close($dbCon);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=ABeeZee:ital@0;1&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');

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
            width: calc(100% - 10px);
            position: fixed;
            top: 1px;
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
            margin-top: 9rem;
            margin-left: 220px;
            padding: 20px;
            background-color: #E1E1E1;
            min-height: 80vh;
        }

        .header-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        .header-container h1 {
            margin: 0;
        }

        .header-container svg {
            margin-right: 15px;
        }

        .container {
            margin-top: 10px;
        }

        .booking-details {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            margin-left: 150px;
            display: flex;
            justify-content: space-between;
        }

        .booking-details,
        .table-container {
            width: 70%;
        }

        .table-container table {
            width: 70%;
            border-collapse: collapse;
            margin-left: 50px;
        }

        .table-container table,
        .table-container th,
        .table-container td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .table-container th {
            background-color: #f2f2f2;
        }

        .booking-details img {
            max-width: 100%;
            max-height: 200px;
            /* Adjust height as needed */
            object-fit: contain;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            margin-top: 80px;
            margin-right: 100px;
        }

        .booking-details h2 {
            margin-top: 0;
        }

        .booking-details p {
            margin: 5px 0;
        }

        .btn-theme {
            padding: 10px 20px;
            background-color: #000000;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: Poppins;
            font-size: 16px;
            text-decoration: none;
        }

        .btn-theme:hover {
            background-color: #878787;
        }

        .back-button {
            width: 20px;
            height: 20px;
            cursor: pointer;
            margin-right: 10px;
        }

        .button {
            margin-left: 46rem;
        }
    </style>
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
        <p>
            <hl>ADMIN PORTAL</hl>
        </p>
    </div>

    <div class="sidebar">
        <a href="bookingList.php" class="active">Manage Booking</a>
        <a href="carList.php">Cars</a>
        <a href="feedbackList.php">Feedback List</a>
    </div>

    <div class="main-content">
        <div class="header-container">
            <a href="bookingList.php"><img class="back-button" src="image/back.svg"></a>
            <h1>BOOKING DETAILS</h1>
        </div>
        <div class="booking-details">
            <div class="table-container">
                <table>
                    <tr>
                        <th>Booking ID</th>
                        <td><?php echo $booking['bookID']; ?></td>
                    </tr>
                    <tr>
                        <th>Booking Date</th>
                        <td><?php echo $booking['bookDate']; ?></td>
                    </tr>
                    <tr>
                        <th>Customer Name</th>
                        <td><?php echo $booking['custName']; ?></td>
                    </tr>
                    <tr>
                        <th>Car ID</th>
                        <td><?php echo $booking['carID']; ?></td>
                    </tr>
                    <tr>
                        <th>Car Name</th>
                        <td><?php echo $booking['carName']; ?></td>
                    </tr>
                    <tr>
                        <th>Start Date</th>
                        <td><?php echo $booking['startDate']; ?></td>
                    </tr>
                    <tr>
                        <th>Start Time</th>
                        <td><?php echo $booking['startTime']; ?></td>
                    </tr>
                    <tr>
                        <th>End Date</th>
                        <td><?php echo $booking['endDate']; ?></td>
                    </tr>
                    <tr>
                        <th>End Time</th>
                        <td><?php echo $booking['endTime']; ?></td>
                    </tr>
                    <tr>
                        <th>Total Hours</th>
                        <td><?php echo $booking['totalHour']; ?></td>
                    </tr>
                </table>
            </div>
            <div class="image-container">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($booking['drivingLicense']); ?>" alt="Driving License">
            </div>
        </div>

        <?php if ($status == "Pending") : ?>
            <div class="button">
                <form method="POST">
                    <input type="hidden" name="bookID" value="<?php echo $bookID; ?>">
                    <button type="submit" name="reject" class="btn-theme">Reject Booking</button>
                    <button type="submit" name="confirm" class="btn-theme">Confirm Booking</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>