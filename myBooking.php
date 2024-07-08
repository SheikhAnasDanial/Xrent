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
    <link rel="stylesheet" href="bill.css" type="text/css">
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
            font-family: 'Inter', sans-serif;
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
    </style>
</head>

<body>
    <header class="header">
        <img class="logo" src="image/logo.svg" alt="Logo XRENT">
        <nav class="navbar">
            <a class="page" href="homepage.php">HOME</a>
            <a class="page" href="cars.php">CARS</a>
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
                    <th>CAR NAME</th>
                    <th>BOOK DATE</th>
                    <th>STATUS</th>
                    <th>BILL</th>
                    <th>FEEDBACK</th>
                </tr>

                <?php
                $sql = "SELECT b.bookID, c.carName, b.bookDate, b.bookStatus, b.receiptProof
                FROM booking b
                JOIN car c ON b.carID = c.carID
                WHERE b.custID = ?
                ORDER BY b.bookID ASC";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no . "</td>";
                        echo "<td>" . $row["bookID"] . "</td>";
                        echo "<td>" . $row["carName"] . "</td>";
                        echo "<td>" . $row["bookDate"] . "</td>";
                        echo "<td>" . $row["bookStatus"] . "</td>";
                        echo "<td><a href='billReceipt.php?bookID=" . $row["bookID"] . "' target='_blank'>Print Bill</a></td>";

                        // Check if feedback exists for this booking
                        $fb_sql = "SELECT fbID FROM feedback WHERE bookID = ?";
                        $fb_stmt = $conn->prepare($fb_sql);
                        $fb_stmt->bind_param("s", $row["bookID"]);
                        $fb_stmt->execute();
                        $fb_result = $fb_stmt->get_result();

                        if ($fb_result->num_rows > 0) {
                            echo "<td>Submitted</td>";
                        } else if ($row["bookStatus"] == "Rejected") {
                            echo "<td>No Feedback</td>";
                        } else if ($row["bookStatus"] == "Pending") {
                            echo "<td></td>";
                        } else {
                            echo "<td><a href='feedbackform.php?bookID=" . $row["bookID"] . "'>Submit Feedback</a></td>";
                        }

                        echo "</tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='7'>No bookings found.</td></tr>";
                }

                $stmt->close();
                ?>
            </table>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>
