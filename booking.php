<?php
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'dbConnect.php';

$user_id = $_SESSION['user_id'];
$carID = isset($_GET['carID']) ? $_GET['carID'] : '';

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

$sql = "SELECT * FROM car WHERE carID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $carID);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();
$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOOKING</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=ABeeZee:ital@0;1&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

        body {
            background: #E1E1E1;
            margin: 0;
            padding-top: 70px;
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

        .container {
            margin-top: 2rem;
            color: #000;
            font-family: Poppins;
            font-style: normal;
            line-height: 130%;
            letter-spacing: -0.72px;
            margin-left: 1rem;
            display: flex;
        }

        .left-container {
            flex: 1;
            margin-left: 50px;
            margin-right: 70px;
            justify-content: center;
        }

        .right-container {
            flex: 1;
            margin-right: 40px;
            margin-top: -2rem;
            justify-content: center;
        }

        .form-container {
            background-color: antiquewhite;
            margin-top: -5px;
        }

        .container-feature {
            display: flex;
        }

        .car-container h2 {
            color: #000;
            text-align: center;
            font-family: Poppins;
            font-size: 36px;
            font-weight: 550;
            line-height: 24px;
        }

        .car-img {
            border-radius: 3px;
            border: 1px solid #000000;
            margin-bottom: 27px;
            width: 458px;
            height: auto;
            margin: 0 auto;
            margin-left: 100px;
        }

        .container-pricing,
        .container-features {
            margin-bottom: 10px;
            border-radius: 12px;
            background: var(--Color, #FFF);
            box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
        }

        .head {
            border-radius: 12px 12px 0px 0px;
            background: #000;
            color: var(--Color, #FFF);
            font-family: Poppins;
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: 24px;
            padding-left: 20px;
            padding-top: 10px;
        }

        .details {
            padding: 15px;
            display: flex;
            color: #000;
            font-family: Poppins;
            font-size: 20px;
            font-style: normal;
            font-weight: 500;
            line-height: 15px;
        }

        .types-fuel,
        .gearbox-seat {
            padding-left: 20px;
            padding-right: 20px;
            margin-top: -15px;
        }

        h2 {
            color: #000;
            text-align: center;
            font-family: Poppins;
            font-size: 36px;
            font-style: normal;
            font-weight: 700;
            line-height: 39px;
        }

        .form-container {
            padding: 30px;
            border-radius: 10px;
            background: var(--Color, #FFF);
            box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
        }

        .booking-form {
            height: auto;
        }

        .input-box {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .field-box {
            margin-top: 10px;
            color: #464646;
            font-family: Montserrat;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 24px;
            width: 697px;
            height: 35px;
            fill: var(--Color, #FFF);
            filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));
            border: none;
            border-radius: 3px;
        }

        .pickup-date-time,
        .dropoff-date-time {
            display: flex;
            margin-bottom: 10px;
        }

        .input-date-time {
            width: 400px;
            margin-top: 50px;
            margin-bottom: 45px;
            justify-content: center;
            align-content: center;
        }

        .input-date {
            margin: 10px;
            color: #464646;
            font-family: Montserrat;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 24px;
            width: 210px;
            height: auto;
            fill: var(--Color, #FFF);
            filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));
            border: none;
            border-radius: 3px;
        }

        .input-time {
            margin: 10px;
            color: #464646;
            font-family: Montserrat;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 24px;
            width: 110px;
            height: auto;
            fill: var(--Color, #FFF);
            filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));
            border: none;
            border-radius: 3px;
        }

        .pickup-date-time h3,
        .dropoff-date-time h3 {
            color: #000;
            font-family: Poppins;
            font-size: 20px;
            font-style: normal;
            font-weight: 600;
            line-height: 30px;
        }

        .error {
            color: red;
            font-family: Poppins;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: 14px;
            margin-bottom: 20px;
        }

        .container-time {
            box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
            border-radius: 4px;
            background: #FFFFFF;
            position: relative;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 100px;
            height: auto;
            box-sizing: border-box;
            margin-top: 10px;
            margin-left: 70px;
            border: none;
            border-radius: 3px;
        }

        label {
            color: #000;
            font-family: Poppins;
            font-size: 20px;
            font-style: normal;
            font-weight: 500;
            line-height: 24px;
        }

        button {
            border-radius: 5px;
            background: #000;
            width: 603px;
            height: 66px;
            flex-shrink: 0;
            color: var(--Color, #FFF);
            text-align: center;
            font-family: Poppins;
            font-size: 32px;
            font-style: normal;
            font-weight: 500;
            line-height: 24px;
            margin-bottom: 20px;
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

    <div class="container">
        <div class="left-container">
            <div class="car-container">
                <h2>CAR MODEL : <?php echo htmlspecialchars($car['carName']); ?></h2>
                <img class="car-img" src="data:image/jpeg;base64,<?php echo base64_encode($car['image']); ?>" alt="<?php echo htmlspecialchars($car['carName']); ?>">
            </div>
            <div class="container-pricing">
                <div class="head">
                    <span class="pricing-info">
                        Pricing Info<br>
                    </span>
                </div>
                <span class="details">
                    Hour based pricing : RM <?php echo htmlspecialchars($car['carRatePerHour']); ?> per hour<br>
                </span>
            </div>
            <div class="container-features">
                <div class="head">
                    <span class="features">
                        Features<br>
                    </span>
                </div>
                <span class="details">
                    <span class="types-fuel">
                        <p class="info">Type : <?php echo htmlspecialchars($car['carType']); ?></p><br>
                        Fuel : <?php echo htmlspecialchars($car['carFuel']); ?><br>
                    </span>
                    <span class="gearbox-seat">
                        <p class="info">Gearbox : <?php echo htmlspecialchars($car['carGear']); ?></p><br>
                        Seat : <?php echo htmlspecialchars($car['carSeatNum']); ?>
                    </span>
                </span>
            </div>
        </div>

        <div class="right-container">
            <h2 class="booking-form">BOOKING FORM</h2>
            <div class="form-container">
                <form class="booking-form" action="bookingConfirmation.php" method="POST" onsubmit="return validateTotalHours();">
                    <div class="input-box">
                        <label for="start-date">Start Date:</label>
                        <input type="date" id="start-date" name="start-date" class="field-box input-date" required>
                    </div>
                    <div class="input-box">
                        <label for="start-time">Start Time (7AM-10PM):</label>
                        <input type="time" id="start-time" name="start-time" class="field-box input-time" min="07:00" max="22:00" required>
                    </div>
                    <div class="input-box">
                        <label for="end-date">End Date:</label>
                        <input type="date" id="end-date" name="end-date" class="field-box input-date" required>
                    </div>
                    <div class="input-box">
                        <label for="end-time">End Time (7AM-10PM):</label>
                        <input type="time" id="end-time" name="end-time" class="field-box input-time" min="07:00" max="22:00" required>
                    </div>
                    <div class="total-container">
                        <div class="total">
                            <label>Total Hours:</label>
                            <p id="total-hours">0</p>
                        </div>
                    </div>
                    <div class="error" id="error-message"></div>
                    <input type="hidden" name="carID" value="<?php echo $carID; ?>">
                    <input type="hidden" name="userID" value="<?php echo $user_id; ?>">
                    <button type="submit" class="btn">Book Now</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const now = new Date();
            const startDatePlus2Days = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 2);
            const nextMonth = new Date(startDatePlus2Days.getFullYear(), startDatePlus2Days.getMonth() + 1, startDatePlus2Days.getDate());

            const startDateInput = document.getElementById("start-date");
            const startTimeInput = document.getElementById("start-time");
            const endDateInput = document.getElementById("end-date");
            const endTimeInput = document.getElementById("end-time");
            const errorDiv = document.getElementById("error-message");
            const totalHoursElement = document.getElementById("total-hours");

            // Restrict start date to 2 days after today until next month
            startDateInput.valueAsDate = startDatePlus2Days;
            startDateInput.min = startDatePlus2Days.toISOString().split("T")[0];
            startDateInput.max = nextMonth.toISOString().split("T")[0];

            const endDatePlus1Day = new Date(startDatePlus2Days.getFullYear(), startDatePlus2Days.getMonth(), startDatePlus2Days.getDate());
            endDateInput.valueAsDate = endDatePlus1Day;
            endDateInput.min = endDatePlus1Day.toISOString().split("T")[0];
            endDateInput.max = nextMonth.toISOString().split("T")[0];

            // Validate end date only after start date is selected
            endDateInput.addEventListener("focus", function() {
                if (!startDateInput.value) {
                    errorDiv.innerText = "Please select the start date first.";
                    endDateInput.blur();
                }
            });

            // Round time inputs to the nearest hour
            function roundToNearestHour(date) {
                date.setMinutes(date.getMinutes() > 30 ? 60 : 0, 0, 0);
                return date;
            }

            startTimeInput.addEventListener("change", function() {
                let startTime = new Date(`1970-01-01T${startTimeInput.value}:00`);
                startTime = roundToNearestHour(startTime);

                // Restrict time from 7am to 10pm
                if (startTime.getHours() < 7 || startTime.getHours() >= 22) {
                    errorDiv.innerText = "Start Time starts at 7 AM until 10 PM.";
                    startTime.setHours(7);
                    startTime.setMinutes(0);
                    startTimeInput.value = startTime.toTimeString().slice(0, 5);
                } else {
                    startTimeInput.value = startTime.toTimeString().slice(0, 5);
                }

                validateTotalHours();
            });

            endTimeInput.addEventListener("change", function() {
                let endTime = new Date(`1970-01-01T${endTimeInput.value}:00`);
                endTime = roundToNearestHour(endTime);

                // Restrict time from 7am to 10pm
                if (endTime.getHours() < 7 || endTime.getHours() >= 22) {
                    errorDiv.innerText = "End Time starts at 7 AM until 10 PM.";
                    endTime.setHours(22);
                    endTime.setMinutes(0);
                    endTimeInput.value = endTime.toTimeString().slice(0, 5);
                } else {
                    endTimeInput.value = endTime.toTimeString().slice(0, 5);
                }

                validateTotalHours();
            });

            // Validate total hours and display
            function validateTotalHours() {
                if (!startDateInput.value || !startTimeInput.value || !endDateInput.value || !endTimeInput.value) {
                    return;
                }

                const startDateTime = new Date(`${startDateInput.value}T${startTimeInput.value}:00`);
                const endDateTime = new Date(`${endDateInput.value}T${endTimeInput.value}:00`);

                if (endDateTime <= startDateTime) {
                    errorDiv.innerText = "End date and time must be after start date and time.";
                    totalHoursElement.innerText = '0';
                    return;
                }

                const totalHours = (endDateTime - startDateTime) / 36e5;

                if (totalHours > 48) {
                    errorDiv.innerText = "The total hours cannot exceed 48 hours.";
                    totalHoursElement.innerText = '0';
                } else {
                    errorDiv.innerText = "";
                    totalHoursElement.innerText = totalHours.toFixed(0);
                    checkBookingAvailability();
                    return true;
                }
            }

            startDateInput.addEventListener("change", function() {
                const startDate = new Date(startDateInput.value);
                const endDatePlus1Day = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() + 1);
                endDateInput.valueAsDate = endDatePlus1Day;
                endDateInput.min = endDatePlus1Day.toISOString().split("T")[0];
                const nextMonthEndDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, startDate.getDate());
                endDateInput.max = nextMonthEndDate.toISOString().split("T")[0];
                validateTotalHours();
            });

            endDateInput.addEventListener("change", validateTotalHours);
        });
    </script>

</body>

</html>