<?php
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Detail</title>
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

        .dropdown .iconarrow {
            margin-left: 5px;
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
            display: flex;
            justify-content: space-between;
            margin-top: 9rem;
            margin-left: 220px;
            padding: 20px;
            background-color: #E1E1E1;
            min-height: 100vh;
        }

        .form-container {
            width: calc(50% - 10px);
        }

        .imagepreview-container {
            width: calc(50% - 10px);
            position: relative;
        }

        .main-content p {
            color: #000;
            font-family: Poppins;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .main-content label {
            font-weight: 600;
        }

        .main-content input,
        .main-content select {
            border-radius: 5px;
            background: var(--Color, #FFF);
            box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .main-content .form-group {
            width: 100%;
            display: block;
            margin-right: 20px;
        }

        .update-btn {
            font-family: Poppins;
            font-size: 16px;
            text-align: center;
            padding: 10px 20px;
            background-color: black;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            display: inline-block;
            margin-left: 10px;
            border: none;
        }

        .update-btn:hover {
            background-color: #676767;
        }

        .cancel-btn {
            text-align: center;
            padding: 10px 20px;
            background-color: black;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            display: inline-block;
        }

        .cancel-btn:hover {
            background-color: #676767;
        }

        .image-preview {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-top: 20px;
        }

        .imagepreview-container p {
            margin-top: 90px;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .availability-radio {
            margin-top: 10px;
            display: flex;
            align-items: center;
        }

        .availability-radio label {
            display: inline-flex;
            align-items: center;
        }

        .availability-radio input[type="radio"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #000;
            outline: none;
            margin-right: 5px;
            position: relative;
            cursor: pointer;
        }

        .availability-radio input[type="radio"]:checked::before {
            content: '';
            width: 10px;
            height: 10px;
            background-color: #000;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
</style>

    </style>
    <script>
        function confirmUpdate() {
            return confirm("Are you sure you want to update this car?");
        }
    </script>
</head>
<body>
    <header class="header">
        <img class="logo" src="image/logo.svg" alt="Logo XRENT">
        <nav class="navbar">
            <div class="dropdown">
                <a href="#">
                    <img class="iconprofile" src="image/icon profile.svg" alt="Icon Profile">
                    <img class="iconarrow" src="image/icon arrow.svg" alt="Icon Arrow">
                </a>
                <div class="dropdown-content">
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
        </nav>
    </header>
    <div class="admin-header">
        <p><hl>ADMIN PORTAL</hl></p>
    </div>
    <div class="sidebar">
        <a href="bookingList.php">Manage Booking</a>
        <a href="carList.php" class="active">Cars</a>
        <a href="feedbackList.php">Feedback List</a>
    </div>
    <div class="main-content">
        <div class="form-container">
            <h1>UPDATE CAR DETAILS</h1>
            <?php
            // Include the database connection file
            require_once "dbConnect.php";

            // Check if carID is provided in the query string
            if (isset($_GET['id'])) {
                $carID = $_GET['id'];


                $dbCon = new mysqli("localhost", "root", "", "xrent");
                // Query to fetch car details based on carID
                $sql = "SELECT * FROM car WHERE carID = '$carID'";
                $result = mysqli_query($dbCon, $sql);

                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    ?>
                    <form action="updateCar.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="carID">Car ID:</label>
                            <input type="text" id="carID" name="carID" value="<?php echo $row['carID']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="carName">Car Name:</label>
                            <input type="text" id="carName" name="carName" value="<?php echo $row['carName']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="carType">Car Type:</label>
                            <select id="carType" name="carType" required>
                                <option value="MPV" <?php if ($row['carType'] == 'MPV') echo 'selected'; ?>>MPV</option>
                                <option value="Sedan" <?php if ($row['carType'] == 'Sedan') echo 'selected'; ?>>Sedan</option>
                                <option value="Small Car" <?php if ($row['carType'] == 'Small Car') echo 'selected'; ?>>Small Car</option>
                                <option value="SUV" <?php if ($row['carType'] == 'SUV') echo 'selected'; ?>>SUV</option>
                                <option value="4x4" <?php if ($row['carType'] == '4x4') echo 'selected'; ?>>4x4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="carBrand">Car Brand:</label>
                            <select id="carBrand" name="carBrand" required>
                                <option value="Perodua" <?php if ($row['carBrand'] == 'Perodua') echo 'selected'; ?>>Perodua</option>
                                <option value="Proton" <?php if ($row['carBrand'] == 'Proton') echo 'selected'; ?>>Proton</option>
                                <option value="Toyota" <?php if ($row['carBrand'] == 'Toyota') echo 'selected'; ?>>Toyota</option>
                                <option value="Nissan" <?php if ($row['carBrand'] == 'Nissan') echo 'selected'; ?>>Nissan</option>
                                <option value="Honda" <?php if ($row['carBrand'] == 'Honda') echo 'selected'; ?>>Honda</option>
                                <option value="Hyundai" <?php if ($row['carBrand'] == 'Hyundai') echo 'selected'; ?>>Hyundai</option>
                                <option value="Volkswagen" <?php if ($row['carBrand'] == 'Volkswagen') echo 'selected'; ?>>Volkswagen</option>
                                <option value="Mazda" <?php if ($row['carBrand'] == 'Mazda') echo 'selected'; ?>>Mazda</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="carSeatNum">Number of Seats:</label>
                            <input type="number" id="carSeatNum" name="carSeatNum" value="<?php echo $row['carSeatNum']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="carGear">Gear Type:</label>
                            <select id="carGear" name="carGear" required>
                                <option value="Automatic" <?php if ($row['carGear'] == 'Automatic') echo 'selected'; ?>>Automatic</option>
                                <option value="Manual" <?php if ($row['carGear'] == 'Manual') echo 'selected'; ?>>Manual</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="carFuel">Fuel Type:</label>
                            <select id="carFuel" name="carFuel" required>
                                <option value="Diesel" <?php if ($row['carFuel'] == 'Diesel') echo 'selected'; ?>>Diesel</option>
                                <option value="Petrol" <?php if ($row['carFuel'] == 'Petrol') echo 'selected'; ?>>Petrol</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Availability:</label>
                            <div class="availability-radio">
                                <label for="available">
                                    <input type="radio" id="carAvailability_yes" name="carAvailability" value="Yes" <?php if ($row['carAvailability'] == 'Yes') echo 'checked'; ?>> Available
                                </label>
                                <label for="not-available">
                                    <input type="radio" id="carAvailability_no" name="carAvailability" value="No" <?php if ($row['carAvailability'] == 'No') echo 'checked'; ?>> Not Available
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="carRatePerHour">Rate per Hour (RM):</label>
                            <input type="text" id="carRatePerHour" name="carRatePerHour" value="<?php echo $row['carRatePerHour']; ?>" required>
                        </div>
                        <div class="form-group">
                            <a href="carList.php" class="cancel-btn">Cancel</a>
                            <button type="submit" class="update-btn" onclick="return confirmUpdate()">Update Car</button>
                        </div>
                    </form>
                    <?php
                } else {
                    echo "<p>No car found with ID: $carID</p>";
                }
            } else {
                echo "<p>Car ID not specified.</p>";
            }

            // Close database connection
            mysqli_close($dbCon);
            ?>
        </div>
        <div class="imagepreview-container">
            <p style="font-weight: 600;">Car Image</p>
            <?php
            // Display current car image
            if (isset($row['image']) && !empty($row['image'])) {
                $imageData = base64_encode($row['image']);
                $src = 'data:image/jpeg;base64,'.$imageData;
                echo '<img class="image-preview" src="' . $src . '" alt="Car Image">';
            } else {
                echo '<p>No image found.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
