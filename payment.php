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

// Fetch user name
$sql = "SELECT custName FROM customer WHERE custID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($custName);
$stmt->fetch();
$stmt->close();

$booking_details = isset($_SESSION['booking_details']) ? $_SESSION['booking_details'] : null;
if (!$booking_details) {
    die("Booking details not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_FILES["receipt_proof"]) || $_FILES["receipt_proof"]["error"] != UPLOAD_ERR_OK) {
        echo "<script>alert('Please insert the receipt file.');</script>";
    }
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["receipt_proof"])) {
    $file = $_FILES['receipt_proof'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];

    // Validate file upload
    if ($fileTmpName) {
        $fileContent = addslashes(file_get_contents($fileTmpName));
        $_SESSION['receipt_proof'] = $fileContent;
        echo "The file " . htmlspecialchars($fileName) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAYMENT</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=ABeeZee:ital@0;1&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        body {
            background: rgba(225, 225, 225, 0.60);
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

        .title {
            margin-top: 5.5rem;
            color: #000;
            font-family: Poppins;
            font-size: 36px;
            font-style: normal;
            font-weight: 700;
            line-height: 130%;
            letter-spacing: -0.72px;
            margin-left: 1rem;
        }

        h2 {
            font-size: 36px;
            font-weight: 700;
            line-height: 39px;
            margin-top: 6%;
            color: #000;
            font-family: Poppins;
            font-size: 36px;
            font-style: normal;
            font-weight: 700;
            line-height: 39px;
            margin-left: 35px;
        }

        .head {
            margin-left: 7px;
        }

        .container {
            font-family: Poppins;
            color: #000;
            font-style: normal;
            display: flex;
            margin-top: -40px;
        }

        .container-left {
            margin-left: 30px;
        }

        th {
            border: 1px solid var(--Color, #FFF);
            background: #000;
            color: var(--Color, #FFF);
            font-family: Poppins;
            font-size: 18px;
            font-style: normal;
            font-weight: 700;
            line-height: 130%;
            padding: 10px 12px;
        }

        td {
            background: #FFF;
            color: rgba(0, 0, 0, 0.73);
            font-family: Poppins;
            font-size: 18px;
            font-style: normal;
            font-weight: 300;
            line-height: 18px;
            padding: 10px 12px;
        }

        .car-model {
            color: rgba(0, 0, 0, 0.73);
            font-family: Poppins;
            font-size: 20px;
            font-style: normal;
            font-weight: 500;
            line-height: 18px;
        }

        .container-right {
            align-items: center;
            justify-content: center;
            margin-left: 2%;
        }

        .head {
            font-size: 25px;
            font-style: normal;
            font-weight: 600;
        }

        .product-head,
        .product {
            width: 407px;
        }

        .subtotal {
            color: rgba(0, 0, 0, 0.73);
            text-align: center;
            font-family: Inter;
            font-size: 20px;
            font-style: normal;
            font-weight: 700;
            line-height: 130%;
        }

        .subtotal-head,
        .subtotal {
            width: 258px;
        }

        .section-payment {
            border-radius: 5px;
            background: #4B4B4B;
            width: 728px;
            height: 317px;
            padding: 5px 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }

        .message {
            padding-top: 20px;
            color: var(--Color, #FFF);
            font-size: 12px;
            font-weight: 500;
            width: 600px;
            height: 54.342px;
            margin-top: 0px;
        }

        .payment-icons {
            border-radius: 5px;
            background: var(--Color, #FFF);
            width: 491px;
            height: 93.59px;
            margin-top: 10%;
        }

        .icon {
            margin-left: 2.5%;
            margin-right: 1%;
            margin-top: 0.5%;
        }

        .section-attach {
            display: flex;
            border-radius: 5px;
            background: var(--Color, #FFF);
            box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
            width: 736px;
        }

        .button-file {
            width: 89px;
            height: 19px;
            margin-top: 2.5%;
            margin-left: 1.5%;
            margin-right: 1.5%;
            margin-bottom: 2.5%;
        }

        .place-order {
            border-radius: 5px;
            background: #000;
            width: 628px;
            height: 50px;
            color: var(--Color, #FFF);
            text-align: center;
            font-family: Poppins;
            font-size: 32px;
            font-style: normal;
            font-weight: 500;
            line-height: 24px;
            margin-top: 15px;
            margin-bottom: 10px;
            margin-left: auto;
            margin-right: auto;
            left: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .attach-receipt {
            display: flex;
        }

        .asterisk {
            color: #F00;
            margin-left: 3px;
        }
    </style>
    <script>
        function displayFileName() {
            const fileInput = document.getElementById('license');
            const fileNameDisplay = document.getElementById('fileNameDisplay');

            const file = fileInput.files[0];
            if (file) {
                fileNameDisplay.textContent = file.name;
            }
        }
    </script>
</head>

<body>
    <header class="header">
        <img class="logo" src="image/logo.svg" alt="Logo XRENT">
        <nav class="navbar">
            <a class="page" href="homepage.php">HOME</a>
            <a class="page active" href="cars.php">CARS</a>
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
    <h2>BILLING DETAILS</h2>
    <div class="container">
        <div class="container-left">
            <p class="head">Your Order</p>
            <table>
                <tr>
                    <th clsss="product-head">Product</th>
                    <th class="subtotal-head">Subtotal</th>
                </tr>
                <tr>
                    <td class="product">
                        <p class="car-model">
                            <?php echo htmlspecialchars($booking_details['carBrand'] . ' ' . $booking_details['carName']); ?>
                        </p>
                        Pickup Date & Time : <br>
                        <?php echo htmlspecialchars($booking_details['formattedStartDateTime']); ?> <br><br>
                        Dropoff Date & Time : <br>
                        <?php echo htmlspecialchars($booking_details['formattedEndDateTime']); ?> <br><br>
                        Total Hours : <?php echo htmlspecialchars($booking_details['totalHours']); ?> Hours <br><br>
                        Car Rate Per Hour : RM <?php echo htmlspecialchars($booking_details['carRatePerHour']); ?><br><br>
                        Pickup Location : <br>
                        XRent Chendering, Kuala Terengganu <br><br>
                        Dropoff Location : <br>
                        XRent Chendering, Kuala Terengganu <br><br>
                    </td>
                    <td class="subtotal">RM <?php echo htmlspecialchars(number_format($booking_details['totalCost'], 2)); ?></td>
                </tr>
                <tr>
                    <td class="product">Deposit</td>
                    <td class="subtotal">RM 100.00</td>
                </tr>
                <tr>
                    <td class="product">Total</td>
                    <td class="subtotal">
                        RM <?php echo htmlspecialchars(number_format((float)$booking_details['totalCost'] + 100.00, 2)); ?>
                    </td>

                </tr>
            </table>
        </div>

        <div class="container-right">
            <p class="head">Payment Method</p>
            <div class="section-payment">
                <div class="message">
                    Make your payment including deposit directly into our bank account. Please use your Book ID as <br>
                    the payment reference. Your order will not be confirm until the funds have cleared in our account. <br>
                    (Please use instant transfer) <br><br>
                    We accept payment via online banking or below : <br>
                </div>
                <div class="payment-icons">
                    <img src="image/icon-tng.png" alt="Icon TnG" class="icon">
                    <img src="image/icon-boost.png" alt="Icon Boost" class="icon">
                    <img src="image/icon-visa.png" alt="Icon Visa" class="icon">
                    <img src="image/icon-mastercard.png" alt="Icon Mastercard" class="icon">
                    <img src="image/icon-grab.png" alt="Icon Grab" class="icon">
                </div>
            </div>
            <div class="section-receipt">
                <div class="attach-receipt">
                    <p>Attach Receipt</p>
                    <p class="asterisk">*</p>
                </div>
                <div class="section-attach">
                    <form action="payment.php" method="POST" enctype="multipart/form-data">
                        <input type="file" id="receipt_proof" name="receipt_proof" accept="image/*,.pdf" onchange="displayFileName()">
                        <p id="fileNameDisplay" style="margin-top: 15px;"></p>
                        <button type="submit">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <a href="bookingProcess.php" style="text-decoration: none;">
        <button class="place-order">PLACE ORDER</button>
    </a>

</body>

</html>