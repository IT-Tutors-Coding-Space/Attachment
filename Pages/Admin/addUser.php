<?php
require_once "../../db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userType = $_POST['userType'];
    $email = $_POST['email'];

    // Input validation
    if (empty($userType) || empty($email)) {
        echo "<script>
                alert('User type and email are required.'); 
                window.history.back();
              </script>";
        exit();
    }

    // Redirect to existing registration pages with email parameter
    $redirectUrl = match($userType) {
        'student' => '../../SignUps/StudentReg.php',
        'company' => '../../SignUps/CompanyReg.php',
        'admin' => '../../SignUps/AdminRegs.php',
        default => false
    };

    if ($redirectUrl) {
        header("Location: $redirectUrl?email=" . urlencode($email));
        exit();
    } else {
        echo "<script>
                alert('Invalid user type selected.'); 
                window.history.back();
              </script>";
        exit();
    }
}
?>