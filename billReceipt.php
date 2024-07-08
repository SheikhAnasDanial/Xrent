<?php
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'dbConnect.php';

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "xrent");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT custName FROM customer WHERE custID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($custName);
$stmt->fetch();
$stmt->close();

// Retrieve bill details based on the latest booking
$sql = "SELECT b.billID, b.billDate, b.totalAmount, b.bookID,
               bo.startDate, bo.startTime, bo.endDate, bo.endTime, bo.totalHour,
               c.carName, c.carRatePerHour
        FROM bill b
        INNER JOIN booking bo ON b.bookID = bo.bookID
        INNER JOIN car c ON bo.carID = c.carID
        WHERE bo.custID = ? 
        ORDER BY b.billDate DESC
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $billID = $row['billID'];
    $billDate = $row['billDate'];
    $totalAmount = $row['totalAmount'];
    $bookID = $row['bookID'];
    $startDate = $row['startDate'];
    $startTime = $row['startTime'];
    $endDate = $row['endDate'];
    $endTime = $row['endTime'];
    $totalHour = $row['totalHour'];
    $carName = $row['carName'];
    $carRatePerHour = $row['carRatePerHour'];
} else {
    // Handle case where no bill data is found
    die("No bill data found.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Receipt</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=ABeeZee:ital@0;1&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');

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
            font-family: ABeeZee;
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

        .page {
            color: var(--Color, #FFF);
            -webkit-text-stroke-width: 1;
            -webkit-text-stroke-color: #000;
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: 24px;
            text-decoration: none;
            margin-right: 2rem;
        }

        .page.active {
            border-bottom: 2px solid white;
        }

        .dropdown {
            position: relative;
            width: 230px;
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
            font-family: Inter;
            font-weight: 400;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        body {
            background-color: #E1E1E1;
        }

        .content {
            padding-top: 80px;
            color: black;
            font-family: Poppins;
            font-weight: 700;
            line-height: 39px;
            word-wrap: break-word;
        }

        .container {
            width: 50%;
            background: white;
            border-radius: 12px;
            margin: 0 auto;
            padding: 20px;
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .left,
        .right {
            display: flex;
            flex-direction: column;
        }

        .main-content {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .left img {
            width: 120px;
            height: 65px;
        }

        .left p {
            color: black;
            font-size: 20px;
            font-family: Poppins;
            font-weight: 400;
            line-height: 39px;
            word-wrap: break-word
        }

        .status {
            width: 160px;
            height: 34px;
            background: #379FFF;
            border-radius: 5px;
            text-align: center;
            color: white;
            font-size: 20px;
            font-family: Poppins;
            font-weight: 600;
            word-wrap: break-word
        }

        .right {
            width: 50%;
        }

        .right img {
            width: 100%;
            height: 100%;
        }

        .right p {
            color: black;
            font-size: 20px;
            font-family: Poppins;
            font-weight: 400;
            line-height: 24px;
            word-wrap: break-word;
        }

        .table-container {
            margin-top: 20px;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            padding: 10px;
            text-align: left;
            font-family: Poppins;
        }

        th {
            background-color: black;
            color: white;
        }

        td h5 {
            font-size: 20px;
        }

        .Total {
            width: 100%;
            height: 100%;
            text-align: right;
            color: black;
            font-size: 20px;
            font-family: Poppins;
            font-weight: 500;
            word-wrap: break-word;
        }

        .print-button {
            color: white;
            background-color: black;
            font-size: 18px;
            font-family: Poppins;
            font-weight: 400;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            line-height: 24px;
            cursor: pointer;
            margin-top: 20px;
            margin-left: 18rem;
        }

        .print-button:hover {
            background-color: #333;
        }
    </style>
</head>

<body>
    <header class="header">
        <img class="logo" src="image/logo.svg" alt="Logo XRENT">
        <nav class="navbar">
            <a class="page" href="home.html">HOME</a>
            <a class="page" href="cars.html">CARS</a>
            <a class="page" href="about.html">ABOUT</a>
            <div class="dropdown">
                <a>
                    <img class="iconprofile" src="image/icon profile.svg" alt="Icon Profile">
                    <p><?php echo htmlspecialchars($custName); ?></p>
                    <img class="iconarrow" src="image/icon arrow.svg" alt="Icon Arrow">
                </a>
                <div class="dropdown-content">
                    <a href="myProfile.html">My Profile</a>
                    <a href="myBooking.html" class="page active">My Booking</a>
                    <a href="logout.html">Log Out</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="content">
        <h1 class="title">BILL RECEIPT</h1>
    </div>

    <div class="Main">
        <div class="container">
            <div class="main-content">
                <div class="left">
                    <img src="image/logo receipt.png" alt="Logo XRENT">
                    <p> Book Date: <?php echo date('d/m/Y', strtotime($billDate)); ?></p>
                    <p class="status">SUCCESSFUL</p>
                </div>
                <div class="right">
                    <img src="image/receipt item.png" alt="Receipt">
                    <p>Bill ID : <?php echo $billID; ?></p>
                    <p>Bill Date : <?php echo date('d/m/Y', strtotime($billDate)); ?></p>
                    <p>Total : RM<?php echo number_format($totalAmount, 2); ?></p>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Booking ID</th>
                        <th>Product</th>
                        <th>Total</th>
                    </tr>
                    <tr>
                        <td><?php echo $bookID; ?></td>
                        <td>
                            <h5><?php echo $carName; ?></h5>
                            <p>Pickup Date & Time :</p>
                            <p><?php echo date('d/m/Y', strtotime($startDate)); ?> at <?php echo date('H:i', strtotime($startTime)); ?></p>
                            <p>Dropoff Date & Time :</p>
                            <p><?php echo date('d/m/Y', strtotime($endDate)); ?> at <?php echo date('H:i', strtotime($endTime)); ?></p>
                            <p>Total Hour : <?php echo $totalHour; ?> hours</p>
                            <p>Total Cost : RM<?php echo number_format(($totalHour * $carRatePerHour), 2); ?></p>
                            <p>Pickup Location : </p>
                            <p>XRent Chendering, Kuala Terengganu</p>
                            <p>Dropoff Location : </p>
                            <p>XRent Chendering, Kuala Terengganu</p>
                        </td>
                        <td>RM<?php echo number_format(($totalHour * $carRatePerHour), 2); ?></td>
                    </tr>
                </table>
                <div class="Total">
                    <hr>
                    <p>DEPOSIT : RM100.00</p>
                    <hr>
                    <p>TOTAL : RM<?php echo number_format($totalAmount, 2); ?></p>
                    <hr>
                </div>
                <div class="print">
                    <input type="submit" id="print" name="print" value="Print" class="print-button"><br>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
