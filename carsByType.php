<?php
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'dbConnect.php';

$user_id = $_SESSION['user_id'];
$carType = isset($_GET['carType']) ? $_GET['carType'] : '';
$carBrand = isset($_GET['carBrand']) ? $_GET['carBrand'] : '';
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

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

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

$sql = "SELECT SQL_CALC_FOUND_ROWS carID, carName, image, carRatePerHour 
        FROM car 
        WHERE carType = ? ";

$params = ["s", $carType];

if ($carBrand) {
    $sql .= "AND carBrand = ? ";
    $params[0] .= "s";
    $params[] = $carBrand;
}

if ($searchQuery) {
    $sql .= "AND carName LIKE ? ";
    $params[0] .= "s";
    $params[] = $searchQuery;
}

$sql .= "LIMIT ?, ?";
$params[0] .= "ii";
$params[] = $start;
$params[] = $limit;

$stmt = $conn->prepare($sql);
$stmt->bind_param(...$params);
$stmt->execute();
$result = $stmt->get_result();

$cars = [];
while ($row = $result->fetch_assoc()) {
    $cars[] = $row;
}
$stmt->close();

$result = $conn->query("SELECT FOUND_ROWS() as total");
$total = $result->fetch_assoc()['total'];
$pages = ceil($total / $limit);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car For Rental</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
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
            margin-left: 2rem;
        }

        .brand-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
            margin-top: -1rem;
            margin-left: 1rem;
            flex-shrink: 0;
        }

        .brand {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            background: rgba(15, 15, 15, 0.05);
            text-align: center;
        }

        .brand img {
            align-content: center;
            justify-content: center;
            height: 50px;
            width: auto;
            margin-top: 1rem;
        }

        .brand h2 {
            color: #0F0F0F;
            text-align: center;
            font-family: ABeeZee;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 24px;
        }

        hr {
            color: #EEE;
        }

        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            margin-bottom: -1rem;
            margin-right: 3rem;
            padding: 0 1rem;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            margin-left: 1rem;
            font-family: Poppins;
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: 24px;
            text-decoration: none;
            color: rgba(0, 0, 0, 0.70);
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: #000;
        }

        .breadcrumb-item a:hover {
            color: grey;
        }

        .search {
            display: flex;
            align-items: center;
        }

        .search input {
            color: rgba(0, 0, 0, 0.70);
            align-content: center;
            font-family: ABeeZee;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 24px;
            width: 160px;
            height: 30px;
            flex-shrink: 0;
            display: flex;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.70);
            background: rgba(15, 15, 15, 0.00);
        }

        .search img {
            margin-left: 1rem;
        }

        .car-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 2rem;
        }

        .carlist {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .car {
            width: 400px;
            height: 220px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            font-family: Poppins;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.50);
            background: rgba(15, 15, 15, 0.03);
        }

        .car h3 {
            color: #000;
            font-family: Poppins;
            font-size: 24px;
            font-style: normal;
            font-weight: 600;
            line-height: 130%;
            letter-spacing: -0.48px;
        }

        .car .left {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 70%;
        }

        .car .right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            width: 30%;
        }

        .car .price {
            margin-top: 2rem;
            color: #000;
            font-family: Poppins;
            font-size: 32px;
            font-style: normal;
            font-weight: 600;
            line-height: 130%;
            letter-spacing: -0.64px;
        }

        .car .per {
            margin-top: -2rem;
            color: #000;
            font-family: Poppins;
            font-size: 20px;
            font-style: normal;
            font-weight: 500;
            line-height: 130%;
            letter-spacing: -0.4px;
        }

        .car img {
            width: 100%;
            height: 100%;
            border-radius: 8px;
        }

        .car button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            border-radius: 40px;
            background: linear-gradient(90deg, #4B4B4B 0%, #0F0F0F 100%);
            color: var(--Color, #FFF);
            text-align: center;
            font-family: Poppins;
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            text-decoration: none;
        }

        .pagination {
            display: flex;
            justify-content: right;
            margin-top: 1rem;
            margin-right: 4rem;
            font-family: Poppins;
        }

        .pagination a {
            color: #000;
            text-decoration: none;
            display: inline-block;
            padding: 8px 16px;
            margin: 0 4px;
            border-radius: 4px;
            border: 1px solid rgba(0, 0, 0, 0.50);
            background: rgba(15, 15, 15, 0.03);
        }

        .pagination a:hover {
            background: rgba(15, 15, 15, 0.1);
        }

        .pagination a.active {
            background: linear-gradient(90deg, #4B4B4B 0%, #0F0F0F 100%);
            color: white;
            border: 1px solid #0F0F0F;
        }
    </style>
    <script>
        function getQueryParam(param) {
            let urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        document.addEventListener("DOMContentLoaded", function() {
            const brandElements = document.querySelectorAll('.brand');

            brandElements.forEach(function(brandElement) {
                brandElement.addEventListener('click', function() {
                    const carBrand = this.getAttribute('data-brand');
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.set('carBrand', carBrand);
                    window.location.search = urlParams.toString();
                });
            });
        });
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

    <h1 class="title">Cars of Type: <?php echo htmlspecialchars($carType); ?><?php echo $carBrand ? " - " . htmlspecialchars($carBrand) : ""; ?></h1>
    <br>
    <div class="brand-container">
        <div class="brand-list">
            <div class="brand" data-brand="Perodua">
                <img src="image/logoperodua.svg" alt="Logo Perodua">
                <h2>Perodua</h2>
            </div>
            <div class="brand" data-brand="Proton">
                <img src="image/logoproton.svg" alt="Logo Proton">
                <h2>Proton</h2>
            </div>
            <div class="brand" data-brand="Honda">
                <img src="image/logohonda.svg" alt="Logo Honda">
                <h2>Honda</h2>
            </div>
            <div class="brand" data-brand="Mazda">
                <img src="image/logomazda.svg" alt="Logo Mazda">
                <h2>Mazda</h2>
            </div>
            <div class="brand" data-brand="Hyundai">
                <img src="image/logohyundai.svg" alt="Logo Hyundai">
                <h2>Hyundai</h2>
            </div>
            <div class="brand" data-brand="Volkswagen">
                <img src="image/logovolk.svg" alt="Logo Volkswagen">
                <h2>Volkswagen</h2>
            </div>
            <div class="brand" data-brand="Toyota">
                <img src="image/logotoyota.svg" alt="Logo Toyota">
                <h2>Toyota</h2>
            </div>
            <div class="brand" data-brand="Nissan">
                <img src="image/logonissan.svg" alt="Logo Nissan">
                <h2>Nissan</h2>
            </div>
        </div>
    </div>
    <hr>
    <div class="search-container">
        <div class="breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="cars.php">Cars</a></li>
                    <?php if ($carBrand) : ?>
                        <li class="breadcrumb-item">
                            <a href="carsByType.php?carType=<?php echo urlencode($carType); ?>">Cars By Type</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php echo htmlspecialchars($carBrand); ?>
                        </li>
                    <?php else : ?>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="carsByType.php?carType=<?php echo urlencode($carType); ?>">Cars By Type</a>
                        </li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
        <div class="search">
            <input type="text" id="search-input" placeholder="Search car" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <a href="#" id="search-button"><img src="image/search.svg" alt="Search"></a>
        </div>
    </div>
    <div class="car-container">
        <div class="carlist">
            <?php foreach ($cars as $car) : ?>
                <div class="car">
                    <div class="left">
                        <h3><?php echo htmlspecialchars($car['carName']); ?></h3>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($car['image']); ?>" alt="<?php echo htmlspecialchars($car['carName']); ?>">
                    </div>
                    <div class="right">
                        <p class="price">RM <?php echo htmlspecialchars($car['carRatePerHour']); ?></p>
                        <p class="per">per hour</p>
                        <button onclick="window.location.href='booking.php?carID=<?php echo urlencode($car['carID']); ?>'">Book</button>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="pagination">
        <?php if ($page > 1) : ?>
            <a href="?page=<?php echo $page - 1; ?>&carType=<?php echo urlencode($carType); ?>&carBrand=<?php echo urlencode($carBrand); ?>">Prev</a>
        <?php endif; ?>
        <a class="active" href="?page=<?php echo $page; ?>&carType=<?php echo urlencode($carType); ?>&carBrand=<?php echo urlencode($carBrand); ?>"><?php echo $page; ?></a>
        <?php if ($page < $pages) : ?>
            <a href="?page=<?php echo $page + 1; ?>&carType=<?php echo urlencode($carType); ?>&carBrand=<?php echo urlencode($carBrand); ?>">Next</a>
        <?php endif; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('search-button').addEventListener('click', function(event) {
                event.preventDefault();
                var searchQuery = document.getElementById('search-input').value;
                var urlParams = new URLSearchParams(window.location.search);
                urlParams.set('q', searchQuery);
                window.location.search = urlParams.toString();
            });
        });
    </script>

</body>

</html>