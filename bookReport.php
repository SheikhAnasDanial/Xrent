<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Report</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=ABeeZee:ital@0;1&display=swap');

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .header-container {
            text-align: center;
        }

        .main-content {
            margin-top: 4rem;
            margin-left: 20px;
            margin-right: 20px;
            padding: 20px;
            background-color: #E1E1E1;
        }

        .book-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }

        .book-list th,
        .book-list td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .book-list th {
            font-family: ABeeZee;
            background-color: #C7C8CC;
            color: black;
            font-weight: bold;
        }

        .book-list td.action a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }

        .book-list td.action a:hover {
            text-decoration: underline;
        }

        .receipt {
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
        }

        .print-button-container {
            text-align: center;
            margin-top: 20px;
        }

        .print-button {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .print-button:hover {
            background-color: #555;
        }

        .book-list th.no,
        .book-list td.no {
            width: 20px; 
        }

        .total-bookings {
            text-align: right;
            margin-top: 10px;
        }

        @media print {
            body * {
                visibility: visible;
            }

            .print-button-container {
                display: none;
            }

            .book-list th {
                -webkit-print-color-adjust: exact;
                background-color: #C7C8CC !important;
                color: black;
            }
        }
    </style>

</head>

<body>
    <div class="main-content">
        <div class="header-container">
            <h1>BOOKING REPORT</h1>
        </div>
        <div class="print-button-container">
            <button class="print-button" onclick="window.print()">PRINT</button>
        </div>

        <?php
        require_once 'dbConnect.php';

        $sql = "SELECT b.bookID, car.carName, c.custName, b.bookDate, 
                    b.totalHour * car.carRatePerHour AS totalAmount, 
                    b.receiptProof
                FROM booking b
                LEFT JOIN car ON b.carID = car.carID
                LEFT JOIN customer c ON b.custID = c.custID";

        $result = $conn->query($sql);
        $totalBookings = $result->num_rows;
        ?>

        <div class="total-bookings">
            Total Bookings: <?php echo $totalBookings; ?>
        </div>

        <table class="book-list">
            <thead>
                <tr>
                    <th class="no">NO</th>
                    <th>BOOK ID</th>
                    <th>CAR NAME</th>
                    <th>CUSTOMER NAME</th>
                    <th>BOOK DATE</th>
                    <th>TOTAL AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($totalBookings > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='no'>" . $no++ . "</td>";
                        echo "<td>" . $row['bookID'] . "</td>";
                        echo "<td>" . $row['carName'] . "</td>";
                        echo "<td>" . $row['custName'] . "</td>";
                        echo "<td>" . $row['bookDate'] . "</td>";
                        echo "<td>RM " . number_format($row['totalAmount'], 2) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No bookings found</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>
