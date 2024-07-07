<?php

session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

require_once 'dbConnect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Car</title>
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
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
        }

        .main-content input,
        .main-content select {
            border-radius: 5px;
            background: #FFF;
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

        .add-car-btn {
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

        .add-car-btn:hover {
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

        .header-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        .header-container h1 {
            margin: 0;
        }

        .required {
            color: red;
        }

        .imagepreview-container {
            margin-top: 90px;
        }

        .image-preview {
            width: 100%;
            height: 470px;
            border: 1px solid #000000;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .choose-image-link {
            font-size: 15px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        input[type="file"] {
            display: none;
        }

        .form-group {
            margin-bottom: 20px;
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
                    <a href="login.html">Log Out</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Admin Header and Sidebar -->
    <div class="admin-header">
        <p>
            <hl>ADMIN PORTAL</hl>
        </p>
    </div>
    <div class="sidebar">
        <a href="bookingList.php">Manage Booking</a>
        <a href="carList.php" class="active">Cars</a>
        <a href="feedbackList.php">Feedback List</a>
    </div>

    <!-- Main Content: Add Car Form -->
    <div class="main-content">
        <div class="form-container">
            <h1>ADD CAR</h1>
            <form action="insertCar.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Car Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="type">Car Type <span class="required">*</span></label>
                    <select id="type" name="type" required>
                        <option value="MPV">MPV</option>
                        <option value="SEDAN">SEDAN</option>
                        <option value="Small CAR">Small CAR</option>
                        <option value="SUV">SUV</option>
                        <option value="4x4">4x4</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="brand">Car Brand <span class="required">*</span></label>
                    <select id="brand" name="brand" required>
                        <option value="Perodua">Perodua</option>
                        <option value="Proton">Proton</option>
                        <option value="Toyota">Toyota</option>
                        <option value="Nissan">Nissan</option>
                        <option value="Honda">Honda</option>
                        <option value="Hyundai">Hyundai</option>
                        <option value="Volkswagen">Volkswagen</option>
                        <option value="Mazda">Mazda</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="seat-no">Seat Number <span class="required">*</span></label>
                    <input type="number" id="seat-no" name="seat-no" required>
                </div>
                <div class="form-group">
                    <label for="gearbox">Gearbox <span class="required">*</span></label>
                    <select id="gearbox" name="gearbox" required>
                        <option value="Manual">Manual</option>
                        <option value="Automatic">Automatic</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fuel">Fuel Type <span class="required">*</span></label>
                    <select id="fuel" name="fuel" required>
                        <option value="Petrol">Petrol</option>
                        <option value="Diesel">Diesel</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Availability <span class="required">*</span></label>
                    <div class="availability-radio">
                        <label for="available">
                            <input type="radio" id="available" name="availability" value="Yes" required> Available
                        </label>
                        <label for="not-available">
                            <input type="radio" id="not-available" name="availability" value="No" required> Not Available
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="rate-per-hour">Rate per Hour (RM) <span class="required">*</span></label>
                    <input type="number" step="0.01" id="rate-per-hour" name="rate-per-hour" required>
                </div>
                <div class="form-group">
                    <a href="carList.php" class="cancel-btn">Cancel</a>
                    <button type="submit" class="add-car-btn">Add Car</button>
                </div>
        </div>
        <div class="imagepreview-container">
            <label for="car-image">Car Image <span class="required">*</span></label>
            <input type="file" id="car-image" name="car-image" id="car-image" accept="image/*" required onchange="previewImage(event)">
            <a class="choose-image-link" href="#" onclick="document.getElementById('car-image').click(); return false;">Choose Image</a>
            <div class="image-preview" id="imagePreview"></div>
        </div>
        </form>
    </div>

    <!-- JavaScript for Image Preview -->
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var imagePreview = document.getElementById('imagePreview');
                imagePreview.innerHTML = '<img src="' + reader.result + '" alt="Car Image">';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

</html>