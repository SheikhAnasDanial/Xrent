<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $icNum = $_POST['icNum'];
    $address = $_POST['address'];
    $phNum = $_POST['phNum'];
    $email = $_POST['email'];
    $password = $_SESSION['password'];


    $conn = new mysqli("localhost", "root", "", "xrent");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT MAX(SUBSTRING(custID, 2)) AS max_id FROM customer";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $max_id = $row['max_id'];

    if ($max_id) {
        $next_id = 'R' . sprintf('%03d', intval($max_id) + 1);
    } else {
        $next_id = 'R001';
    }

    $file = $_FILES['license'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];

    $fileContent = addslashes(file_get_contents($fileTmpName));

    $sql = "INSERT INTO `customer`(`custID`, `custName`, `custIC`, `custAddress`, `custPhone`, `custEmail`, `password`, `drivingLicense`, `role`) 
            VALUES ('$next_id','$name','$icNum','$address','$phNum','$email','$password','$fileContent','Cust')";

    if ($conn->query($sql) === TRUE) {

        $_SESSION['user_id'] = $next_id;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'Cust';

        echo "New Customer record created successfully";
        echo "<script>window.location.href = 'homepage.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
