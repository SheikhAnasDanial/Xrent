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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booking</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=ABeeZee:ital@0;1&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

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

        .container {
            width: 85%;
            height: auto;
            background: white;
            border-radius: 12px;
            margin: 9rem auto 0;
            padding: 60px;
        }

        .content {
            padding-top: 40px;
        }

        .title {
            color: black;
            font-size: 36px;
            font-family: Poppins;
            font-weight: 700;
            line-height: 39px;
            word-wrap: break-word;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: center;
            font-size: 18px;
            font-family: Poppins;
        }

        .table th {
            background-color: #4B4B4B;
            color: white;
            font-size: 16px;
        }

        .table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table tr:hover {
            background-color: #ddd;
        }

        .table a {
            color: #007BFF;
            text-decoration: none;
        }

        .table a:hover {
            text-decoration: underline;
        }

        /* Modal styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1000;
            /* Sit on top */
            padding-top: 100px;
            /* Location of the box */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.9);
            /* Black w/ opacity */
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            height: auto;
            max-height: 80%;
            text-align: center;
        }

        .modal img {
            width: 100%;
            height: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <header class="header">
        <img class="logo" src="image/logo.svg" alt="Logo XRENT">
        <nav class="navbar">
            <a class="page " href="homepage.php">HOME</a>
            <a class="page " href="cars.php">CARS</a>
            <a class="page" href="about.php">ABOUT</a>
            <div class="dropdown">
                <a>
                    <img class="iconprofile" src="image/icon profile.svg" alt="Icon Profile">
                    <p><?php echo htmlspecialchars($custName); ?></p>
                    <img class="iconarrow" src="image/icon arrow.svg" alt="Icon Arrow">
                </a>
                <div class="dropdown-content">
                    <a href="myProfile.php">My Profile</a>
                    <a href="myBooking.php">My Booking</a>
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <div>
            <h2 class="title">MY BOOKING</h2>
            <table class="table">
                <tr>
                    <th>NO</th>
                    <th>BOOK ID</th>
                    <th>CAR ID</th>
                    <th>CAR NAME</th>
                    <th>BOOK DATE</th>
                    <th>STATUS</th>
                    <th>BILL</th>
                    <th>FEEDBACK</th>
                </tr>

                <?php
                $sql = "SELECT b.bookID, b.carID, c.carName, b.bookDate, b.bookStatus, b.receiptProof
                FROM booking b
                JOIN car c ON b.carID = c.carID
                LEFT JOIN bill ON b.bookID = bill.bookID
                ORDER BY b.bookID ASC";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no . "</td>";
                        echo "<td>" . $row["bookID"] . "</td>";
                        echo "<td>" . $row["carID"] . "</td>";
                        echo "<td>" . $row["carName"] . "</td>";
                        echo "<td>" . $row["bookDate"] . "</td>";
                        echo "<td>" . $row["bookStatus"] . "</td>";
                        echo "<td>";
                        if (!empty($row['receiptProof'])) {
                            echo "<a href='#' class='receipt' data-receipt-src='data:image/jpg;base64," . base64_encode($row['receiptProof']) . "'>View Receipt</a>";
                        } else {
                            echo "No Receipt";
                        }
                        echo "</td>";

                        echo "<td><a href='feedbackform.php'>Give Feedback</a></td>";
                        echo "</tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='8'>No bookings found</td></tr>";
                }

                $conn->close();
                ?>
            </table>

            <!-- Modal for receipt image -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <img id="modalImage" src="" alt="Receipt Image">
                </div>
            </div>


            <!-- JavaScript for modal functionality -->
            <script>
                document.querySelectorAll('.receipt').forEach(item => {
                    item.addEventListener('click', event => {
                        let receiptImageSrc = item.getAttribute('data-receipt-src');
                        document.getElementById('modalImage').src = receiptImageSrc;
                        document.getElementById('myModal').style.display = "block";
                    });
                });

                document.querySelector('.close').addEventListener('click', () => {
                    document.getElementById('myModal').style.display = "none";
                });

                window.addEventListener('click', event => {
                    if (event.target === document.getElementById('myModal')) {
                        document.getElementById('myModal').style.display = "none";
                    }
                });
            </script>


        </div>
    </div>
</body>

</html>