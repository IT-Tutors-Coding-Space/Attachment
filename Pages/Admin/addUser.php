<?php
require_once "../../db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userType = $_POST['userType'];
    $email = $_POST['email']; // Assuming email will be collected in the modal

    // Input validation
    if (empty($userType) || empty($email)) {
        echo "<script>alert('User type and email are required.');</script>";
        exit();
    }

    // Redirect based on user type
    switch ($userType) {
        case 'Student':
            header("Location:../../SignUps/StudentReg.php" . urlencode($email));
            break;
        case 'Company':
            header("Location: ../../SignUps/CompanyReg.php" . urlencode($email));
            break;
        case 'Admin':
            header("Location: ../../SignUps/AdminRegs.php" . urlencode($email));
            break;
        default:
            echo "<script>alert('Invalid user type.');</script>";
            exit();
    }
}
?>
