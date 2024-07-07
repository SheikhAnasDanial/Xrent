<?php
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'dbConnect.php';

$user_id = $_SESSION['user_id'];

// Retrieve booking details from session
$booking_details = isset($_SESSION['booking_details']) ? $_SESSION['booking_details'] : null;

if (!$booking_details) {
    die("Booking details not found.");
}

// Retrieve receipt proof file content from session
$receiptProof = isset($_SESSION['receipt_proof']) ? $_SESSION['receipt_proof'] : null;

if (!$receiptProof) {
    die("Receipt proof file not found or invalid.");
}

// Extract other necessary details from booking_details
$carID = $booking_details['carID'];
$startDate = $booking_details['startDate'];
$startTime = $booking_details['startTime'];
$endDate = $booking_details['endDate'];
$endTime = $booking_details['endTime'];
$totalHours = $booking_details['totalHours'];
$carRatePerHour = $booking_details['carRatePerHour'];
$totalCost = $booking_details['totalCost'] + 100.00;

$conn = new mysqli("localhost", "root", "", "xrent");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function generateSequentialBookID($conn) {
    $prefix = 'B';
    $sql = "SELECT bookID FROM booking ORDER BY bookID DESC LIMIT 1";
    $result = $conn->query($sql);
    $lastID = $result->fetch_assoc();
    
    if ($lastID) {
        $lastIDNumber = (int) substr($lastID['bookID'], 1);
        $newIDNumber = $lastIDNumber + 1;
    } else {
        $newIDNumber = 1;
    }
    
    return $prefix . str_pad($newIDNumber, 4, '0', STR_PAD_LEFT);
}

$bookID = generateSequentialBookID($conn);
$bookStatus = "Pending";
$bookingDate = date('Y-m-d H:i:s');
$adminID = "A001";

$sql = "INSERT INTO booking (bookID, bookStatus, bookDate, startDate, startTime, endDate, endTime, totalHour, carRatePerHour, receiptProof, adminID, custID, carID)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssssss", $bookID, $bookStatus, $bookingDate, $startDate, $startTime, $endDate, $endTime, $totalHours, $carRatePerHour, $receiptProof, $adminID, $user_id, $carID);

if ($stmt->execute()) {
    unset($_SESSION['booking_details']);
    header("Location: myBooking.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
