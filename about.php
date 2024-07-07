<?php
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.html");
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
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XRENT Car Rental</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=ABeeZee:ital@0;1&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');

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
        .about-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 120px 80px 40px 80px;
            background-color: #F5F5F5;
            color: #000;
        }

        .about-text {
            flex: 1;
            margin-right: 20px;
        }

        .about-text h2, .about-text p {
            margin-left: 95px;
        }

        .about-text h2 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .about-text p {
            font-size: 18px;
            font-weight: 400;
            line-height: 1.6;
        }

        .about-image {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .about-image img {
            max-width: 100%;
            height: auto;
        }

        .goal-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 40px 80px;
            background-color: #FFF;
            color: #000;
        }

        .goal-text {
            flex: 1;
            margin-right: 40px;
        }

        .goal-text h2 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .goal-text p {
            font-size: 18px;
            font-weight: 400;
            line-height: 1.6;
        }

        .goal-image {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .goal-image img {
            max-width: 100%;
            height: auto;
        }
    </style>
    </head>
    <body>
        <header class="header">
            <img class="logo" src="image/logo.svg" alt="Logo XRENT">
            <nav class="navbar">
                <a class="page " href="homepage.php">HOME</a>
                <a class="page " href="cars.php">CARS</a>
                <a class="page active" href="about.php">ABOUT</a>
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
        <section class="about-section">
            <div class="about-image">
                <img src="image/carimg1.png" alt="Car Selection">
            </div>
            <div class="about-text">
                <h2>About XRENT</h2>
                <p>Renting a car is as effortless as a tap on your phone screen. Browse through a wide selection of cars, pick the perfect one for your needs, and book it instantly. Our system ensures that your chosen vehicle is ready when you need it, and our secure payment process guarantees a hassle-free experience.</p>
            </div>
        </section>
    
        <section class="goal-section">
            <div class="goal-text">
                <h2>Our Goal</h2>
                <p>Our goal is to make car rental as easy as ordering takeout. We're here to provide a wonderful experience from booking to billing, so you can focus on hitting the road with confidence and ease.</p>
            </div>
            <div class="goal-image">
                <img src="image/servicecar.jpg" alt="Handing Car Keys">
            </div>
        </section>
    </body>