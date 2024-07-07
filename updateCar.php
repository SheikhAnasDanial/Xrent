<?php
session_start();
require_once "dbConnect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data using $_POST
    $carID = $_POST['carID'];
    $carName = $_POST['carName'];
    $carType = $_POST['carType'];
    $carBrand = $_POST['carBrand'];
    $carSeatNum = $_POST['carSeatNum'];
    $carGear = $_POST['carGear'];
    $carFuel = $_POST['carFuel'];
    $carAvailability = $_POST['carAvailability'];
    $carRatePerHour = $_POST['carRatePerHour'];

    // Update query
    $sql = "UPDATE car SET 
            carName = '$carName', 
            carType = '$carType', 
            carBrand = '$carBrand', 
            carSeatNum = '$carSeatNum', 
            carGear = '$carGear', 
            carFuel = '$carFuel', 
            carAvailability = '$carAvailability', 
            carRatePerHour = '$carRatePerHour'
            WHERE carID = '$carID'";

    $dbCon = new mysqli("localhost", "root", "", "xrent");
    if (mysqli_query($dbCon, $sql)) {
        $_SESSION['success_message'] = "Car details updated successfully.";
        header("Location: carList.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error updating car details: " . mysqli_error($dbCon);
        header("Location: carDetail.php?id=$carID");
        exit();
    }
}

mysqli_close($dbCon);
