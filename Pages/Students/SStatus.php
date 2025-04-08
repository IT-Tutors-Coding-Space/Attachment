<?php
require_once "../../db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location:  ../SignUps/SLogin.php");
    exit();
}

$student_id = $_SESSION["user_id"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status</title>
    <link rel="stylesheet" href="../../CSS/SStatus.css">
</head>
<body>
    <div class="container">
        <h1>Application Status</h1>

        <!-- Progress Bar Section -->
        <div class="status-box">
            <div id="status-bar" class="status-bar"></div>
        </div>

        <!-- Status Text -->
        <div id="status-text" class="status-text">Status: 0%</div>

        <!-- Buttons to simulate different application states -->
        <div class="buttons">
            <button onclick="updateStatus('apply')">Apply</button>
            <button onclick="updateStatus('approved')">Approved</button>
            <button onclick="updateStatus('picked')">Picked</button>
            <button onclick="updateStatus('declined')">Declined</button>
        </div>
    </div>

    <script src="../../Javascript/CNotifications.js"></script>
</body>
</html>
