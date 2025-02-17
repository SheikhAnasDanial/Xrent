<?php
session_start();
require_once 'dbConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $dbCon = new mysqli("localhost", "root", "", "xrent");
    // Generate carID
    $query = "SELECT MAX(SUBSTRING(carID, 2)) AS max_id FROM car";
    $result = mysqli_query($dbCon, $query);

    if (!$result) {
        die("Error: " . mysqli_error($dbCon));
    }

    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];
    $new_id = intval($max_id) + 1;
    $carID = 'C' . sprintf('%03d', $new_id);

    $carName = mysqli_real_escape_string($dbCon, $_POST['name']);
    $carType = mysqli_real_escape_string($dbCon, $_POST['type']);
    $carBrand = mysqli_real_escape_string($dbCon, $_POST['brand']);
    $seatNumber = mysqli_real_escape_string($dbCon, $_POST['seat-no']);
    $gearbox = mysqli_real_escape_string($dbCon, $_POST['gearbox']);
    $fuelType = mysqli_real_escape_string($dbCon, $_POST['fuel']);
    $availability = mysqli_real_escape_string($dbCon, $_POST['availability']);
    $ratePerHour = mysqli_real_escape_string($dbCon, $_POST['rate-per-hour']);

    if (isset($_FILES['car-image']) && $_FILES['car-image']['error'] == UPLOAD_ERR_OK) {
        $fileTmpName = $_FILES['car-image']['tmp_name'];
        $fileContent = addslashes(file_get_contents($fileTmpName));

        $sql = "INSERT INTO `car` (`carID`, `carName`, `carType`, `carBrand`, `carSeatNum`, `carGear`, `carFuel`, `carAvailability`, `image`, `carRatePerHour`)
                VALUES ('$carID', '$carName', '$carType', '$carBrand', '$seatNumber', '$gearbox', '$fuelType', '$availability', '$fileContent', '$ratePerHour')";

        if (mysqli_query($dbCon, $sql)) {
            $_SESSION['success_message'] = "New car record created successfully";
            header("Location: carList.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error: " . $sql . "<br>" . mysqli_error($dbCon);
            header("Location: carList.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Please choose a file or check the file upload settings.";
        if (isset($_FILES['car-image']['error'])) {
            $_SESSION['error_message'] .= " File upload error: " . $_FILES['car-image']['error'];
        }
        header("Location: carList.php");
        exit();
    }

    mysqli_close($dbCon);
}
?>
