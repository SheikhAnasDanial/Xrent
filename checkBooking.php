<?php
include 'dbConnect.php';

// Assuming you have sanitized inputs or used prepared statements in dbConnect.php

$carID = $_POST['carID'];
$startDate = $_POST['start-date'];
$startTime = $_POST['start-time'];
$endDate = $_POST['end-date'];
$endTime = $_POST['end-time'];

// Establish database connection (assuming dbConnect.php handles this securely)
$conn = new mysqli("localhost", "root", "", "xrent");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL statement to check for overlapping bookings
$sql = "SELECT bookID FROM booking 
        WHERE carID = ? 
        AND (
            (endDate > ? OR (endDate = ? AND endTime > ?))   -- New booking ends after existing booking starts
            AND
            (startDate < ? OR (startDate = ? AND startTime < ?))  -- New booking starts before existing booking ends
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $carID, $startDate, $startDate, $startTime, $endDate, $endDate, $endTime);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are overlapping bookings
if ($result->num_rows > 0) {
    echo "<script>alert('The selected start date and time overlap with an existing booking.');</script>";
} else {
    echo "<script>alert('The selected start date and time are valid.');</script>";
}

$stmt->close();
$conn->close();
?>
