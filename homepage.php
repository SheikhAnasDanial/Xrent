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

        body {
            background: url('image/bg car.jpeg') no-repeat center center/cover;
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
            z-index: 100;
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

        .hero {
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: right;
            color: white;
            text-align: left;
            margin-right: 3rem;
            margin-top: 3rem;
        }

        .hero h1 {
            color: var(--Color, #FFF);
            font-family: Poppins;
            font-size: 36px;
            font-style: normal;
            font-weight: 300;
            line-height: 39px;
            letter-spacing: 2.16px;
            margin-bottom: 15px;
        }

        .hero button {
            border-radius: 40px;
            background: var(--Color, #FFF);
            font-family: Poppins;
            border: none;
            padding: 11px 50px;
            color: rgb(16, 16, 16);
            font-size: 1.05em;
            cursor: pointer;
            transition: background 0.5s;
        }

        .hero button:hover {
            background-color: #eae6d5;
        }

        footer {
            background: rgba(139, 125, 125, 0.4);
            color: white;
            padding: 20px 0;
            text-align: left;
            font-family: Poppins;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            max-width: 1200px;
            margin: auto;
            color: #f8f7f7;
        }

        .footer-services,
        .footer-contact {
            font-size: 1em;
        }

        .footer-services h2,
        .footer-contact h2 {
            margin-bottom: 10px;
        }

        .footer-services ul.footer-services {
            list-style-type: circle;
            padding: 0;
        }

        .footer-services ul li {
            margin: 5px 0;
        }
    </style>
</head>

<body>
 
    <header class="header">
        <img class="logo" src="image/logo.svg" alt="Logo XRENT">
        <nav class="navbar">
            <a class="page active" href="homepage.php">HOME</a>
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

    <section class="hero">
        <div class="hero-content">
            <h1>Unlock Your Journey,<br>Rent Your Wheels.</h1>
            <button onclick="window.location.href='cars.php'">BOOK NOW</button>
        </div>
    </section>

    <footer>
        <div class="footer-container">
            <img class="logo" src="image/logo.svg" alt="Logo XRENT">
            <div class="footer-services">
                <h2>Our Services</h2>
                <ul>
                    <li>Top Condition</li>
                    <li>Worry Free</li>
                    <li>Safety & Comfortable</li>
                </ul>
            </div>
            <div class="footer-contact">
                <h2>Contact Us</h2>
                <p>Phone: +6013-567 9982</p>
                <p>Email: infoxrent@gmail.com</p>
                <p>Address: XRent Cendering, 21080 Kuala Terengganu, Terengganu</p>
            </div>
        </div>
    </footer>

</body>

</html>