<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'dbConnect.php';

$user_id = $_SESSION['user_id'];

// Fetching bookID from the URL parameter
if (isset($_GET['bookID'])) {
    $bookID = $_GET['bookID'];
} else {
    echo "<script>alert('Invalid Booking ID');</script>";
    header("Location: myBooking.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $experience = $_POST['experience'];
    $feedbackMessage = $_POST['feedback-message'];

    // Prepare and bind parameters for feedback insertion
    $sql = "INSERT INTO feedback (fbID, fbDescription, fbRating, bookID, fbDate) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // Check for errors in preparing the statement
    if (!$stmt) {
        echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
        exit(); // Stop execution
    }

    // Retrieve the last fbID from the feedback table to determine the next number
    $sql_last_id = "SELECT MAX(SUBSTRING(fbID, 2)) AS max_id FROM feedback";
    $result_last_id = $conn->query($sql_last_id);
    if ($result_last_id && $result_last_id->num_rows > 0) {
        $row = $result_last_id->fetch_assoc();
        $max_id = $row['max_id'];
        $next_id = $max_id + 1;
    } else {
        $next_id = 1; // If no existing feedback, start from 1
    }

    // Format fbID as F001, F002, etc.
    $fbID = 'F' . sprintf('%03d', $next_id);

    // Current date for fbDate
    $fbDate = date('Y-m-d');

    // Bind parameters
    $stmt->bind_param("ssiss", $fbID, $feedbackMessage, $experience, $bookID, $fbDate);

    // Execute statement
    if ($stmt->execute()) {
        echo "<script>alert('Feedback submitted successfully!'); window.location.href = 'myBooking.php';</script>";
        exit(); // Stop execution after redirection
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=ABeeZee:ital@0;1&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');

        body {
            font-family: Arial, sans-serif;
            background-color: #F5F5F5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
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
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(43, 21, 21, 0.911);
            width: 500px;
            margin-top: 100px;
        }

        .container h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-family: 'Poppins', sans-serif;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 98%;
            padding: 3.5px;
            border: 1px solid #D9D9D9;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group input[type="text"] {
            height: 45px;
        }

        .form-group input[type="date"] {
            height: 45px;
        }

        .form-group select {
            height: 45px;
        }

        .form-group textarea {
            color: #000;
            height: 100px;
            resize: none;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
        }

        .buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
        }

        .buttons .cancel-btn {
            background-color: #ccc;
        }

        .buttons .submit-btn {
            background-color: black;
            color: white;
        }
    </style>
</head>
<body>
    <header class="header">
    <header class="header">
        <img class="logo" src="image/logo.svg" alt="Logo XRENT">
        <nav class="navbar">
            <a class="page" href="homepage.php">HOME</a>
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
    </header>

    <div class="container">
        <h1 class="title">Feedback Form</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?bookID=" . $bookID; ?>" method="POST">
            <div class="form-group">
                <label for="experience">Rating:</label>
                <select id="experience" name="experience" required>
                    <option value="" disabled selected>Select rating</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="form-group">
                <label for="feedback-message">Feedback Message:</label>
                <textarea id="feedback-message" name="feedback-message" rows="4" cols="50" placeholder="Share Your Feedback Message." required></textarea>
            </div>
            <div class="buttons">
                <button type="button" class="cancel-btn" onclick="window.location.href='myBooking.php';">Cancel</button>
                <button type="submit" name="submit" class="submit-btn">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>
