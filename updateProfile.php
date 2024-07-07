<?php
session_start();

include 'dbConnect.php';

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "xrent");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $icNum = $_POST['icNum'];
    $address = $_POST['address'];
    $phNum = $_POST['phNum'];
    $email = $_POST['email'];
    $password = $_SESSION['password'];

    $sql = "UPDATE customer SET custName=?, custIC=?, custAddress=?, custPhone=?, custEmail=?";

    if (!empty($_FILES['license']['tmp_name'])) {
        $fileTmpName = $_FILES['license']['tmp_name'];
        $fileContent = addslashes(file_get_contents($fileTmpName));
        $sql .= ", drivingLicense='$fileContent'";
    }

    $sql .= " WHERE custID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name, $icNum, $address, $phNum, $email, $user_id);

    if ($stmt->execute()) {
        echo "Profile updated successfully";
        echo "<script>window.location.href = 'homepage.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
