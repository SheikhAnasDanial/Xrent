<?php
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking List</title>
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
            position: relative;
            width: 120px;
            height: 45px;
            border: 1px solid #000;
            background: #FFF;
            margin-right: 3rem;
        }

        .dropdown a {
            color: black;
            display: flex;
            align-items: center;
            text-decoration: none;
            padding: 0 1rem;
            height: 100%;
            font-size: 18px;
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
            min-height: 75vh;
        }

        .book-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }

        .book-list th,
        .book-list td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .book-list th {
            background-color: #000000;
            color: #fff;
        }

        .book-list td.action a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }

        .book-list td.action a:hover {
            text-decoration: underline;
        }

        .search-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 10px;
            margin-top: 0;
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
            margin-right: 15px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            font-size: 20px;
            font-weight: 400;
            line-height: 24px;
            color: rgba(0, 0, 0, 0.7);
        }

        .breadcrumb a {
            text-decoration: none;
            color: #000;
        }

        .breadcrumb a:hover {
            color: grey;
        }


        .receipt {
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
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

        /* Modal styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1000;
            /* Sit on top */
            padding-top: 100px;
            /* Location of the box */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.9);
            /* Black w/ opacity */
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            height: auto;
            max-height: 80%;
            text-align: center;
        }

        .modal img {
            width: 100%;
            height: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
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
                    <a href="adminDashboard.php">Dashboard</a>
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="admin-header">
        <p>ADMIN PORTAL</p>
    </div>

    <div class="sidebar">
        <a href="bookingList.php" class="active">Manage Booking</a>
        <a href="carList.php">Cars</a>
        <a href="feedbackList.php">Feedback List</a>
    </div>

    <div class="main-content">
        <div class="header-container">
            <h1>BOOKING LIST</h1>
            <form method="GET" action="bookingList.php">
                <div class="search-container">
                    <input type="text" id="search" name="search" placeholder="Search by Book ID">
                    <button type="submit">
                        <img src="image/search.svg" alt="Search">
                    </button>
                </div>
            </form>
        </div>

        <div class="breadcrumb">
            <?php if (isset($_GET['search']) && !empty($_GET['search'])) : ?>
                <a href="bookingList.php">Booking List</a>
                &nbsp;&nbsp;&nbsp;
                Search Results for "<?php echo htmlspecialchars($_GET['search']); ?>"
            <?php endif; ?>
        </div>


        <table class="book-list">
            <thead>
                <tr>
                    <th>BOOK ID</th>
                    <th>CAR NAME</th>
                    <th>CUSTOMER NAME</th>
                    <th>BOOK DATE</th>
                    <th>TOTAL AMOUNT</th>
                    <th>RECEIPT</th>
                    <th>BOOKING STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include database connection
                require_once 'dbConnect.php';

                // Pagination variables
                $results_per_page = 5; // Number of results per page
                if (!isset($_GET['page'])) {
                    $page = 1; // Default page number
                } else {
                    $page = $_GET['page'];
                }

                $dbCon = new mysqli("localhost", "root", "", "xrent");
                // SQL query to retrieve bookings data with totalAmount calculation
                $sql = "SELECT b.bookID, car.carName, c.custName, b.bookDate, 
                            b.totalHour * car.carRatePerHour AS totalAmount, 
                            b.receiptProof, b.bookStatus
                        FROM booking b
                        LEFT JOIN car ON b.carID = car.carID
                        LEFT JOIN customer c ON b.custID = c.custID";

                // Append WHERE clause if search parameter is provided
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $search = mysqli_real_escape_string($dbCon, $_GET['search']);
                    $sql .= " WHERE b.bookID LIKE '%$search%'";
                }

                $dbCon = new mysqli("localhost", "root", "", "xrent");
                // Execute query to get total number of results
                $result = mysqli_query($dbCon, $sql);
                $number_of_results = mysqli_num_rows($result);
                $number_of_pages = ceil($number_of_results / $results_per_page);

                // Calculate the starting point for the results on the current page
                $this_page_first_result = ($page - 1) * $results_per_page;

                // Modify SQL query to include LIMIT for pagination
                $sql .= " LIMIT $this_page_first_result, $results_per_page";

                // Execute SQL query
                $result = mysqli_query($dbCon, $sql);

                // Display bookings data in table
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['bookID'] . "</td>";
                        echo "<td>" . $row['carName'] . "</td>";
                        echo "<td>" . $row['custName'] . "</td>";
                        echo "<td>" . $row['bookDate'] . "</td>";
                        echo "<td>RM " . number_format($row['totalAmount'], 2) . "</td>";
                        echo "<td>";
                        if (!empty($row['receiptProof'])) {
                            echo "<a href='#' class='receipt' data-receipt-src='data:image/jpg;base64," . base64_encode($row['receiptProof']) . "'>View Receipt</a>";
                        } else {
                            echo "No Receipt";
                        }
                        echo "</td>";
                        echo "<td>";
                        if ($row['bookStatus'] == 'Confirmed') {
                            echo "Confirmed";
                        } elseif ($row['bookStatus'] == 'Rejected') {
                            echo "Rejected";
                        } else {
                            echo "Pending";
                        }
                        echo "</td>";
                        echo "<td class='action'>
                            <a href='bookingDetails.php?bookID={$row['bookID']}&status={$row['bookStatus']}'>View Details</a>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No bookings found</td></tr>";
                }
                mysqli_close($dbCon);
                ?>
            </tbody>
        </table>

        <!-- Modal for receipt image -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <img id="modalImage" src="" alt="Receipt Image">
            </div>
        </div>

        <div class="pagination-box">
            <div class="pagination">
                <?php
                // Display pagination links
                for ($page = 1; $page <= $number_of_pages; $page++) {
                    echo "<a href='bookingList.php?page=$page'>$page</a>";
                }
                ?>
            </div>
        </div>

        <!-- JavaScript for modal functionality -->
        <script>
            document.querySelectorAll('.receipt').forEach(item => {
                item.addEventListener('click', event => {
                    let receiptImageSrc = item.getAttribute('data-receipt-src');
                    document.getElementById('modalImage').src = receiptImageSrc;
                    document.getElementById('myModal').style.display = "block";
                });
            });

            document.querySelector('.close').addEventListener('click', () => {
                document.getElementById('myModal').style.display = "none";
            });

            window.addEventListener('click', event => {
                if (event.target === document.getElementById('myModal')) {
                    document.getElementById('myModal').style.display = "none";
                }
            });
        </script>
</body>

</html>