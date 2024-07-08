<?php
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $_SESSION['email'] = $email;
    $_SESSION['password'] = $password;
}

include 'dbConnect.php';

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "xrent");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT custName, custIC, custAddress, custPhone, custEmail, drivingLicense FROM customer WHERE custID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($custName, $custIC, $custAddress, $custPhone, $custEmail, $drivingLicense);
$stmt->fetch();
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=ABeeZee:ital@0;1&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        body {
            background-color: #E1E1E1;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
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
            font-family: 'ABeeZee', sans-serif;
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

        .container {
            width: 55%;
            background: white;
            border-radius: 12px;
            margin: 9rem auto 0;
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        .left,
        .right {
            flex: 1;
        }

        .left {
            margin-right: 20px;
        }

        .right {
            margin-top: 5rem;
        }

        .title {
            text-align: center;
            font-size: 24px;
            margin-left: 20rem;
        }

        label {
            color: black;
            font-size: 18px;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="file"] {
            color: #464646;
            font-size: 18px;
            padding: 8px;
            margin-bottom: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        .edit-button {
            color: white;
            background-color: black;
            font-size: 18px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }

        .edit-button:hover {
            background-color: #333;
        }
    </style>
    <script>
        function displayImage() {
            const fileInput = document.getElementById('license');
            const selectedImage = document.getElementById('selectedImage');

            const file = fileInput.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                selectedImage.src = e.target.result;
                selectedImage.style.display = 'block';
            }

            reader.readAsDataURL(file);
        }

        function loadImageFromServer() {
            const selectedImage = document.getElementById('selectedImage');
            const imageData = '<?php echo base64_encode($drivingLicense); ?>';
            if (imageData) {
                selectedImage.src = 'data:image/jpeg;base64,' + imageData;
                selectedImage.style.display = 'block';
            }
        }

        function validateForm() {
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phNum').value;
            const icNum = document.getElementById('icNum').value;
            const email = document.getElementById('email').value;
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            const icPattern = /^[0-9]{12}$/;
            const phonePattern = /^[0-9]{10,11}$/;
            const namePattern = /^[A-Za-z\s]+$/; // Pattern to ensure name is alphabetic

            // Check if any field is empty
            if (name === '' || phone === '' || icNum === '' || email === '') {
                alert('Please fill in all fields.');
                return false;
            }

            // Check if phone and icNum are numeric
            if (isNaN(phone) || isNaN(icNum)) {
                alert('Phone number and IC Number must be numeric.');
                return false;
            }

            // Check if name is alphabetic
            if (!namePattern.test(name)) {
                alert('Name must be alphabetic.');
                return false;
            }

            // Validate email format
            if (!emailPattern.test(email)) {
                alert('Please enter a valid email address.');
                return false;
            }

            // Validate IC number format
            if (!icPattern.test(icNum)) {
                alert('Please enter a valid IC Number (12 digits, numeric only).');
                return false;
            }

            // Validate phone number format
            if (!phonePattern.test(phone)) {
                alert('Please enter a valid phone number (10-11 digits, numeric only).');
                return false;
            }

            return true;
        }
    </script>
</head>

<body onload="loadImageFromServer()">
    <header class="header">
        <img class="logo" src="image/logo.svg" alt="Logo XRENT">
        <nav class="navbar">
            <a class="page" href="homepage.php">HOME</a>
            <a class="page" href="cars.php">CARS</a>
            <a class="page" href="about.php">ABOUT</a>
            <div class="dropdown">
                <a href="#">
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
            <h2 class="title">MY PROFILE</h2>
            <div class="left">
                <form method="post" action="updateProfile.php" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Name" value="<?php echo htmlspecialchars($custName); ?>" required>

                    <label for="icNum">IC Number</label>
                    <input type="text" id="icNum" name="icNum" placeholder="XXXXXXXXXXXX" value="<?php echo htmlspecialchars($custIC); ?>" pattern="[0-9]{12}" title="IC Number must be 12 digits" required>

                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Address" value="<?php echo htmlspecialchars($custAddress); ?>" required>

                    <label for="phNum">Phone Number</label>
                    <input type="text" id="phNum" name="phNum" placeholder="01XXXXXXXX" value="<?php echo htmlspecialchars($custPhone); ?>" pattern="[0-9]{10,11}" title="Phone Number must be 10-11 digits" required>

                    <label for="email">Email Address</label>
                    <input type="text" id="email" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($custEmail); ?>" required>
            </div>
        </div>
        <div class="right">
            <label for="license">Driving License</label>
            <input type="file" id="license" name="license" accept="image/*" onchange="displayImage()">

            <img id="selectedImage" src="#" alt="Selected Image" style="display: none; max-width: 300px; max-height: 300px;">

            <input type="submit" value="Update" class="edit-button">
            </form>
        </div>
    </div>
</body>

</html>