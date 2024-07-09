<?php
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['cancel_booking'])) {
    unset($_SESSION['booking_details']);
    header("Location: homepage.php");
    exit();
}

include 'dbConnect.php';

$user_id = $_SESSION['user_id'];
$carID = isset($_POST['carID']) ? $_POST['carID'] : '';
$startDate = isset($_POST['start-date']) ? $_POST['start-date'] : '';
$startTime = isset($_POST['start-time']) ? $_POST['start-time'] : '';
$endDate = isset($_POST['end-date']) ? $_POST['end-date'] : '';
$endTime = isset($_POST['end-time']) ? $_POST['end-time'] : '';
$totalHours = isset($_POST['total-hours']) ? $_POST['total-hours'] : '';

if (empty($startDate) || empty($startTime) || empty($endDate) || empty($endTime)) {
    die("Please fill in all fields.");
}

$startDateTime = new DateTime($startDate . ' ' . $startTime);
$endDateTime = new DateTime($endDate . ' ' . $endTime);

$formattedStartDateTime = $startDateTime->format('d/m/Y \a\t H:iA');
$formattedEndDateTime = $endDateTime->format('d/m/Y \a\t H:iA');

if ($endDateTime <= $startDateTime) {
    die("End date and time must be after start date and time.");
}

$totalHours = ($endDateTime->getTimestamp() - $startDateTime->getTimestamp()) / 3600;

if ($totalHours > 48) {
    die("The total hours cannot exceed 48 hours.");
}

$conn = new mysqli("localhost", "root", "", "xrent");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL statement to check for overlapping bookings
$sql = "SELECT bookID FROM booking 
        WHERE carID = ? 
        AND (
            (endDate > ? OR (endDate = ? AND endTime > ?))   -- New booking ends after existing booking starts
            AND
            (startDate < ? OR (startDate = ? AND startTime < ?))  -- New booking starts before existing booking ends
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $carID, $startDate, $startDate, $startTime, $endDate, $endDate, $endTime);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are overlapping bookings
if ($result->num_rows > 0) {
    echo "<script>
            alert('The selected start date and time overlap with an existing booking.');
            window.location.href='booking.php?carID=" . $carID . "';
          </script>";
} else {
    echo "<script>alert('The selected start date and time are valid.')</script>";
}


$stmt->close();

// Fetch user name
$sql = "SELECT custName FROM customer WHERE custID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($custName);
$stmt->fetch();
$stmt->close();

// Fetch car details
$sql = "SELECT * FROM car WHERE carID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $carID);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();
$stmt->close();

$sql = "SELECT carRatePerHour FROM car WHERE carID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $carID);
$stmt->execute();
$stmt->bind_result($carRatePerHour);
$stmt->fetch();
$stmt->close();

$totalCost = $totalHours * $carRatePerHour;
$startDateTime = $startDate . ' ' . $startTime;
$endDateTime = $endDate . ' ' . $endTime;

// Store booking details in session
$_SESSION['booking_details'] = [
    'carID' => $carID,
    'custName' => $custName,
    'carBrand' => $car['carBrand'],
    'carName' => $car['carName'],
    'startDate' => $startDate,
    'endDate' => $endDate,
    'startTime' => $startTime,
    'endTime' => $endTime,
    'totalHours' => $totalHours,
    'carRatePerHour' => $carRatePerHour,
    'totalCost' => $totalCost,
    'formattedStartDateTime' => $formattedStartDateTime,
    'formattedEndDateTime' => $formattedEndDateTime
];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOOKING CONFIRMATION</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=ABeeZee:ital@0;1&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

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

        body {
            background: #E1E1E1;
        }

        h1 {
            margin-top: 90px;
            margin-left: 30px;
            color: #000;
            font-family: Poppins;
            font-size: 36px;
            font-style: normal;
            font-weight: 700;
            line-height: 39px;
        }

        .cart-container {
            padding: 10px 10px;
            margin-top: 20px;
            width: 1430px;
        }

        .car-container,
        .cart-container {
            border-radius: 10px;
            background: var(--Color, #FFF);
            margin-left: 30px;
        }

        .car-container {
            display: flex;
            width: 1450px;
            padding-top: 10px;
        }

        .cancel-icon {
            margin-top: 10%;
            margin-bottom: 10%;
            margin-left: 2%;
        }

        .car-img {
            margin-top: 6%;
            margin-right: 1%;
            margin-left: 1%;
            width: 258px;
            height: auto;
        }

        .car-img img {
            border: 1px solid #000;
        }

        .car-container,
        .cart-container,
        button {
            color: #000;
            font-family: Poppins;
            font-style: normal;
            line-height: 24px;
        }

        .head,
        .head-product {
            font-size: 23px;
            font-weight: 600;
        }

        .head {
            text-align: center;
        }

        .head-product {
            margin-left: 10%;
        }

        .car-model {
            font-size: 20px;
            font-weight: 500;
        }

        .car-model,
        .car-details {
            margin-left: 10%;
        }

        .car-details {
            font-size: 18px;
            font-weight: 300;
            width: 500px;
            line-height: 30px;
        }

        .price {
            margin-top: 130px;
            font-size: 20px;
            font-weight: 500;
            width: 90px;
            text-align: center;
        }

        .price-column {
            margin-left: 70%;
        }

        .subtotal-column {
            margin-left: 100%;
        }

        .cart-container h2 {
            font-size: 28px;
            font-weight: 500;
            height: 5px;
        }

        .cart-section,
        h2 {
            margin-left: 20px;
        }

        .cart-section {
            display: flex;
            font-size: 26px;
            font-weight: 400;
        }

        .column {
            display: flex;
        }

        .subtotal-total,
        .subtotal-total-price {
            font-size: 20px;
            font-weight: 500;
        }

        .subtotal-total-price {
            margin-left: 83%;
        }

        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        button {
            border-radius: 10px;
            background: #000;
            width: 1400px;
            height: 70px;
            color: var(--Color, #FFF);
            text-align: center;
            font-size: 32px;
            font-weight: 500;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <h1>YOUR ORDER</h1>
    <div class="container">
        <div class="car-container">
            <div class="cancel-icon">
                <a href="bookingConfirmation.php?cancel_booking=true"><img class="cancel-icon" src="image/cancel-icon.svg" alt="Cancel Icon"></a>
            </div>

            <div class="car-img">
                <img class="car-img" src="data:image/jpeg;base64,<?php echo base64_encode($car['image']); ?>" alt="<?php echo htmlspecialchars($car['carName']); ?>">
            </div>
            <div class="product">
                <p class="head-product">Product</p>
                <p class="car-model"><?php echo htmlspecialchars($car['carBrand'] . ' ' . $car['carName']); ?></p>
                <div class="car-details">
                    Pickup Date & Time : <?php echo htmlspecialchars($formattedStartDateTime); ?><br>
                    Dropoff Date & Time : <?php echo htmlspecialchars($formattedEndDateTime); ?><br>
                    Total Hour : <?php echo htmlspecialchars($totalHours); ?> Hours<br>
                    Car Rate Per Hour : RM <?php echo htmlspecialchars($carRatePerHour); ?><br>
                    Pickup Location : XRent Chendering, Kuala Terengganu <br>
                    Dropoff Location : XRent Chendering, Kuala Terengganu <br>
                </div>
            </div>
            <div class="column">
                <div class="price-column">
                    <p class="head">Price</p>
                    <p class="price">RM <?php echo htmlspecialchars($totalCost); ?></p>
                </div>
                <div class="subtotal-column">
                    <p class="head">Subtotal</p>
                    <p class="price">RM <?php echo htmlspecialchars($totalCost); ?></p>
                </div>
            </div>
        </div>

        <div class="cart-container">
            <h2>Cart Totals</h2>
            <div class="cart-section">
                <div class="subtotal-total">
                    <p>Subtotal</p>
                    <p>Total</p>
                </div>
                <div class="subtotal-total-price">
                    <p>RM <?php echo htmlspecialchars($totalCost); ?></p>
                    <p>RM <?php echo htmlspecialchars($totalCost); ?></p>
                </div>
            </div>
            <div class="button-container">
                <a href="payment.php"><button>PROCEED TO CHECKOUT</button></a>
            </div>

        </div>

    </div>
</body>

</html>