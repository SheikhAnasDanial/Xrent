<?php
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'dbConnect.php';

$user_id = $_SESSION['user_id'];
$sql_admin_name = "SELECT adminName FROM admin WHERE adminID = '$user_id'";
$result_admin_name = $conn->query($sql_admin_name);
$adminName = $result_admin_name->fetch_assoc()['adminName'];

$adminNameLength = strlen($adminName);

// Close the database connection after fetching admin name
$conn->close();

// Initialize variables for search and pagination
$search = '';
$whereClause = '';
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Calculate offset for pagination

// Check if search parameter is set
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    // Prepare WHERE clause for SQL query
    $whereClause = " WHERE carName LIKE '%$search%'";
}

// Query to fetch cars with pagination and search
$dbCon = new mysqli("localhost", "root", "", "xrent");

// Query to count total number of records with search filter
$sql_total = "SELECT COUNT(carID) AS total FROM car" . $whereClause;
$result_total = mysqli_query($dbCon, $sql_total);
$data_total = mysqli_fetch_assoc($result_total);
$total_pages = ceil($data_total['total'] / $limit);

// Query to fetch cars with pagination and search
$sql = "SELECT * FROM car" . $whereClause . " LIMIT $limit OFFSET $offset";
$result = mysqli_query($dbCon, $sql);

// Check if there are any rows returned
$carsFound = mysqli_num_rows($result) > 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car List</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=ABeeZee:ital@0;1&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

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
            font-family: 'Poppins', sans-serif;
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
            margin-top: 9rem;
            margin-left: 220px;
            padding: 20px;
            background-color: #E1E1E1;
            min-height: 80vh;
        }

        .car-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }

        .car-list th,
        .car-list td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .car-list th {
            background-color: #000000;
            color: #fff;
        }

        .car-list td.action a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }

        .car-list td.action a:hover {
            text-decoration: underline;
        }

        .search-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }

        .search-container input {
            padding: 8px;
            margin-right: 15px;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.70);
            background-color: #fff;
            font-size: 14px;
            width: 150px;
            height: 25px;
        }

        .search-container img {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            margin-right: 15px;
            margin-top: 10px;
        }

        .add-car-btn {
            margin-right: 15px;
            padding: 10px 20px;
            background-color: #000000;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
        }

        .add-car-btn:hover {
            background-color: #676767;
        }

        .pagination-box {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        .pagination {
            display: flex;
            align-items: right;
        }

        .pagination a {
            text-decoration: none;
            color: #000000;
            padding: 5px 10px;
            border: 1px solid #000000;
            border-radius: 5px;
            margin: 0 2px;
        }

        .pagination a:hover {
            background-color: #878787;
            color: #fff;
        }

        .pagination span {
            padding: 5px 10px;
            background-color: #878787;
            color: #ffffff;
            border: 1px solid #D9D9D9;
            border-radius: 5px;
            margin: 0 2px;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0px;
            margin-top: 20px;
        }

        .header-container h1 {
            margin: 0;
        }

        .success-message {
            color: green;
            font-size: 16px;
            margin: 10px 0;
        }

        .error-message {
            color: red;
            font-size: 16px;
            margin: 10px 0;
        }

        .car-list td.action a.success {
            color: blue;
        }

        .car-list td.action a.error {
            color: red;
        }
        
        #message-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            width: 300px;
        }

        .message {
            position: relative;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            margin-bottom: 10px;
            animation: slideIn 0.5s forwards, fadeOut 0.5s forwards 6.5s;
            display: none;
        }

        .error {
            background-color: #f44336; /* Red */
        }

        @keyframes slideIn {
            0% {
                transform: translateY(-100%);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }
    </style>
    <script>
        function showConfirmation(carID) {
            if (confirm("Are you sure you want to delete this car?")) {
                window.location.href = 'deleteCar.php?id=' + carID;
            } else {
                return false;
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const adminNameLength = <?php echo $adminNameLength; ?>;
            const dropdown = document.querySelector('.dropdown');
            dropdown.style.width = `${adminNameLength * 1 + 200}px`; 

            const searchButton = document.getElementById('search-button');
            const searchForm = document.getElementById('search-form');
            
            searchButton.addEventListener('click', function() {
                searchForm.submit();
            });
        });
    </script>
</head>

<body>
    <header class="header">
        <img class="logo" src="image/logo.svg" alt="Logo XRENT">
        <nav class="navbar">
            <div class="dropdown">
                <a href="#">
                    <img class="iconprofile" src="image/icon profile.svg" alt="Icon Profile">
                    <p style="text-align: center;"><span><?php echo $adminName; ?></span></p>
                    <img class="iconarrow" src="image/icon arrow.svg" alt="Icon Arrow">
                </a>
                <div class="dropdown-content">
                    <a href="adminDashboard.php">Dashboard</a>
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
        </nav>
    </header>
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
    <div class="main-content">
        <div class="header-container">
            <h1>CAR LIST</h1>
            <form method="GET" action="carList.php" id="search-form">
                <div class="search-container">
                    <a href="addCar.php" class="add-car-btn">ADD CAR</a>
                    <input type="text" id="search" name="search" placeholder="Search by Car List">
                    <a href="#" id="search-button"><img src="image/search.svg" alt="Search"></a>
                </div>
            </form>
        </div>

        <div class="breadcrumb">
            <?php if (isset($_GET['search']) && !empty($_GET['search'])) : ?>
                Search Results for "<?php echo htmlspecialchars($_GET['search']); ?>"
            <?php endif; ?>
        </div>

        <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']);
            }

            if (isset($_GET['status']) && isset($_GET['message'])) {
                $status = $_GET['status'];
                $message = $_GET['message'];
                echo '<div class="alert ' . ($status == 'success' ? 'alert-success' : 'alert-error') . '">' . htmlspecialchars($message) . '</div>';
            }

            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">';
                echo $_SESSION['success_message'];
                echo '</div>';
                unset($_SESSION['success_message']); 
            }
        ?>
        <table class="car-list">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>CAR ID</th>
                    <th>TYPE</th>
                    <th>BRAND</th>
                    <th>NAME</th>
                    <th>AVAILABILITY</th>
                    <th class="action">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Initialize an index counter for numbering rows
                $index = $offset + 1;

                if ($carsFound) {
                    // Loop through each row of data
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $index . "</td>";
                        echo "<td>" . $row['carID'] . "</td>";
                        echo "<td>" . $row['carType'] . "</td>";
                        echo "<td>" . $row['carBrand'] . "</td>";
                        echo "<td>" . $row['carName'] . "</td>";
                        echo "<td>" . ($row['carAvailability'] == 'Yes' ? 'AVAILABLE' : 'NOT AVAILABLE') . "</td>";
                        echo '<td class="action"><a class="success" href="carDetail.php?id=' . $row['carID'] . '">Edit</a> <a class="error" href="#" onclick="return showConfirmation(\'' . $row['carID'] . '\')">Delete</a></td>';
                        echo "</tr>";
                        $index++;
                    }
                } else {
                    echo "<tr><td colspan='7'>No cars found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="pagination-box">
            <div class="pagination">
                <?php if ($page > 1) : ?>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <?php if ($i == $page) : ?>
                        <span><?php echo $i; ?></span>
                    <?php else : ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page < $total_pages) : ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>