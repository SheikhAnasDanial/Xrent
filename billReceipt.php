<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
include 'dbConnect.php';

$user_id = $_SESSION['user_id'];

// Retrieve customer name
$stmt = $conn->prepare("SELECT custName FROM customer WHERE custID = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($custName);
$stmt->fetch();
$stmt->close();

// Retrieve bookID from URL
if (isset($_GET['bookID'])) {
    $bookID = $_GET['bookID'];
} else {
    die("Invalid request: Missing bookID parameter.");
}

// Retrieve bill details based on the bookID
$stmt = $conn->prepare("SELECT b.billID, b.billDate, b.totalAmount, b.bookID,
                               bo.startDate, bo.startTime, bo.endDate, bo.endTime, bo.totalHour,
                               c.carName, c.carRatePerHour
                        FROM bill b
                        INNER JOIN booking bo ON b.bookID = bo.bookID
                        INNER JOIN car c ON bo.carID = c.carID
                        WHERE bo.bookID = ? AND bo.custID = ?
                        ORDER BY b.billDate DESC
                        LIMIT 1");
$stmt->bind_param("ss", $bookID, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $billID = $row['billID'];
    $billDate = $row['billDate'];
    $totalAmount = $row['totalAmount'];
    $startDate = $row['startDate'];
    $startTime = $row['startTime'];
    $endDate = $row['endDate'];
    $endTime = $row['endTime'];
    $totalHour = $row['totalHour'];
    $carName = $row['carName'];
    $carRatePerHour = $row['carRatePerHour'];
} else {
    die("No bill data found for this booking.");
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
        /* CSS styles for the receipt */
        @import url('https://fonts.googleapis.com/css2?family=ABeeZee:ital@0;1&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');

        body {
            background-color: #E1E1E1;
            font-family: Poppins, Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }

        .header img {
            max-width: 120px;
            height: auto;
        }

        .bill-details {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .bill-details .left,
        .bill-details .right {
            flex: 1;
        }

        .status {
            background-color: #379FFF;
            width: 200px;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            text-align: center;
            margin-top: 10px;
        }

        .table-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        .total {
            margin-top: 20px;
            text-align: right;
            text-align: center; /* Center contents horizontally */
        }

        .print-button {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .print-button:hover {
            background-color: #555;
        }

        @media print {
            th {
                -webkit-print-color-adjust: exact; /* Ensure accurate color printing */
                background-color: #f2f2f2 !important;
                color: #333;
            }

            .status {
                -webkit-print-color-adjust: exact; 
                background-color: #379FFF !important;
                color: #fff;
                padding: 5px 10px;
                border-radius: 5px;
                text-align: center;
                margin-top: 10px;
            }
            
            .print-button-container {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="image/logo receipt.png" alt="Logo XRENT" class="logo">
            <img style="margin-left: 100px;" src="image/receipt item.png" alt="Receipt" class="receipt-item">
        </div>
        <div class="bill-details">
            <div class="left">
                <p class="status">BOOKING SUCCESSFUL</p>
                <p>Customer Name: <?php echo $custName; ?></p>
            </div>
            <div class="right">
                <p style="text-align: right;">Bill ID: <?php echo $billID; ?></p>
                <p style="text-align: right;">Bill Date: <?php echo date('d/m/Y', strtotime($billDate)); ?></p>
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
                        <p><strong><?php echo $carName; ?></strong></p>
                        <p>Pickup: <?php echo date('d/m/Y', strtotime($startDate)); ?> at <?php echo date('H:i', strtotime($startTime)); ?></p>
                        <p>Dropoff: <?php echo date('d/m/Y', strtotime($endDate)); ?> at <?php echo date('H:i', strtotime($endTime)); ?></p>
                        <p>Total Hours: <?php echo $totalHour; ?></p>
                        <p>Total Cost: RM <?php echo number_format(($totalHour * $carRatePerHour), 2); ?></p>
                    </td>
                    <td>RM <?php echo number_format(($totalHour * $carRatePerHour), 2); ?></td>
                </tr>
            </table>
        </div>
        <div class="total">
            <hr>
            <p style="text-align: right;">Deposit: RM 100.00</p>
            <p style="text-align: right;">Total Amount: RM <?php echo number_format($totalAmount, 2); ?></p>
            <hr>
            <div class="print-button-container">
                <button class="print-button" onclick="window.print()">Print</button>
            </div>
        </div>
    </div>
</body>

</html>
