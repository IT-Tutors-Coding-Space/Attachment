<?php
require_once "db.php";
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

// Validate status value
$allowedStatuses = ['Accepted', 'Rejected', 'Pending'];
if (!in_array($status, $allowedStatuses)) {
    die("Invalid status value");
}

try {
    // Update application status
    $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE applications_id = ?");
    $stmt->execute([$status, $applicationId]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("No application found with ID: $applicationId");
    }
    
    // Redirect back with success message
    header("Location: ../Admin/AApplications.php?success=Application status updated to $status");
    exit();
} catch (PDOException $e) {
    // Log error and redirect back
    error_log("Failed to update application status: " . $e->getMessage());
    header("Location: ../Admin/AApplications.php?error=Failed to update application status");
    exit();
} catch (Exception $e) {
    error_log($e->getMessage());
    header("Location: ../Admin/AApplications.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
