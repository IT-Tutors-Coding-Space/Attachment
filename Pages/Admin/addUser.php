<?php
require_once "../../db.php";
session_start();

if ($_SESSION["role"] !== "admin") {
    header("Location: ../../SignUps/ALogin.php");
    exit();
}

require "../../Components/AdminNav.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userType = $_POST['userType'];
    
    // Input validation
    if (empty($userType)) {
        echo "<script>
                alert('Please select a user type.'); 
                window.history.back();
              </script>";
        exit();
    }

    // Redirect to respective registration page
    $redirectUrl = match($userType) {
        'student' => '../../SignUps/StudentReg.php',
        'company' => '../../SignUps/CompanyReg.php',
        'admin' => '../../SignUps/AdminRegs.php',
        default => false
    };

    if ($redirectUrl) {
        header("Location: $redirectUrl");
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php require "../../Components/AdminNav.php"; ?>

    <div class="container py-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>Add New User</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">User Type</label>
                        <select class="form-select" name="userType" required>
                            <option value="">Select User Type</option>
                            <option value="student">Student</option>
                            <option value="company">Company</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Continue</button>
                    <a href="AUsers.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>