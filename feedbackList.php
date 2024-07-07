<?php
include 'dbConnect.php';

// Pagination variables
$results_per_page = 5; // Number of results per page
if (!isset($_GET['page'])) {
    $page = 1; // Default page number
} else {
    $page = $_GET['page'];
}

// SQL query to retrieve feedback with proper table joins
$sql = "SELECT f.fbID, b.bookID, c.custName, f.fbDescription, f.fbRating, f.fbDate
        FROM feedback f
        LEFT JOIN booking b ON f.bookID = b.bookID
        LEFT JOIN customer c ON b.custID = c.custID";

$filter_rating = isset($_GET['rating']) ? intval($_GET['rating']) : null;
if (!is_null($filter_rating)) {
    $sql .= " WHERE f.fbRating = $filter_rating";
}

$sql .= " ORDER BY f.fbID DESC";

$dbCon = new mysqli("localhost", "root", "", "xrent");
// Execute SQL query with pagination
$result = mysqli_query($dbCon, $sql);
$number_of_results = mysqli_num_rows($result);
$number_of_pages = ceil($number_of_results / $results_per_page);
$this_page_first_result = ($page - 1) * $results_per_page;

$sql .= " LIMIT $this_page_first_result, $results_per_page";
$result = mysqli_query($dbCon, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback List</title>
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
            position: relative;
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
            margin-left: 220px;
            padding: 20px;
            background-color: #E1E1E1; /* Changed background color */
            min-height: 100vh;
        }

        .feedback-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff; /* Table background color */
        }

        .feedback-list th, .feedback-list td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .feedback-list th {
            background-color: #000000;
            color: #fff;
        }

        .feedback-list td.action a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }

        .feedback-list td.action a:hover {
            text-decoration: underline;
        }

        .search-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #000;
            font-family: ABeeZee;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: 24px;
            margin-top: 20px;
        }

        .search-container img {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            margin-right: 15px;
        }

        .receipt {
            color: #007bff;
            text-decoration: underline;
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
            margin-bottom: 20px;
            margin-top: 20px;
        }

        .header-container h1 {
            margin: 0;
        }

        .filter-container {
            display: flex;
            align-items: center;
        }

        .filter-container p {
            margin-right: 10px;
        }

        .filter-container select {
            padding: 8px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
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

    <div class="admin-header">
        <p><hl>ADMIN PORTAL</hl></p>
    </div>

    <div class="sidebar">
        <a href="bookingList.php">Booking</a>
        <a href="carList.php">Cars</a>
        <a href="feedbackList.php" class="active">Feedback List</a>
    </div>

    <div class="main-content">
        <div class="header-container">
            <h1>FEEDBACK LIST</h1>
            <div class="filter-container">
                <form action="feedbackList.php" method="GET">
                    <p>Filter by Rating:</p>
                    <select name="rating" id="rating" onchange="this.form.submit()">
                        <option value="" disabled selected>Select Rating</option>
                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($filter_rating == $i) echo 'selected'; ?>><?php echo $i; ?></option>
                        <?php } ?>
                    </select>
                </form>
            </div>
        </div>
        <table class="feedback-list">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>BOOK ID</th>
                    <th>FEEDBACK ID</th>
                    <th>CUSTOMER NAME</th>
                    <th>DESCRIPTION</th>
                    <th>RATING</th>
                    <th>FEEDBACK DATE</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    $no = ($page - 1) * $results_per_page + 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $row['bookID'] . "</td>";
                        echo "<td>" . $row['fbID'] . "</td>";
                        echo "<td>" . $row['custName'] . "</td>";
                        echo "<td>" . $row['fbDescription'] . "</td>";
                        echo "<td>" . $row['fbRating'] . "</td>";
                        echo "<td>" . $row['fbDate'] . "</td>"; 
                        echo "</tr>";
                    }
                } else {
                    echo '<tr><td colspan="7">No records found</td></tr>';
                }
                mysqli_close($dbCon);
                ?>
            </tbody>
        </table>

        <div class="pagination-box">
            <div class="pagination">
                <?php if ($page > 1) { ?>
                    <a href="feedbackList.php?page=<?php echo ($page - 1); ?>">Prev</a>
                <?php } ?>
                <?php for ($page_num = 1; $page_num <= $number_of_pages; $page_num++) { ?>
                    <a href="feedbackList.php?page=<?php echo $page_num; ?>" <?php if ($page == $page_num) echo 'class="active"'; ?>><?php echo $page_num; ?></a>
                <?php } ?>
                <?php if ($page < $number_of_pages) { ?>
                    <a href="feedbackList.php?page=<?php echo ($page + 1); ?>">Next</a>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>
