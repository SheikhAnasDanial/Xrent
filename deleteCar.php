<?php
session_start();
require_once "dbConnect.php"; // Ensure dbConnect.php has your database connection code

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $carID = $_GET['id'];

    // SQL to delete car based on carID
    $sql = "DELETE FROM car WHERE carID = '$carID'";

    $dbCon = new mysqli("localhost", "root", "", "xrent");
    if (mysqli_query($dbCon, $sql)) {
        $_SESSION['success_message'] = "Car deleted successfully";
    } else {
        $_SESSION['error_message'] = "Error deleting car: " . mysqli_error($dbCon);
    }
} else {
    $_SESSION['error_message'] = "Invalid car ID";
}


// Redirect back to carList.php
header("Location: carList.php");
exit();
?>
