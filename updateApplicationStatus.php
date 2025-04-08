<?php
require_once "../../db.php";
session_start();

// Check if admin is logged in
if ($_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/Alogin.php");
    exit();
}

// Validate input
if (!isset($_POST['applications_id']) || !isset($_POST['status'])) {
    die("Invalid request parameters");
}

$applicationId = $_POST['applications_id'];
$status = $_POST['status'];

try {
    // Update application status
    $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE applications_id = ?");
    $stmt->execute([$status, $applicationId]);
    
    // Redirect back with success message
    header("Location: ../Admin/AApplications.php?success=Application status updated successfully");
    exit();
} catch (PDOException $e) {
    // Redirect back with error message
    header("Location: ../Admin/AApplications.php?error=Failed to update application status");
    exit();
}
?>
