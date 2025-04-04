<?php
require_once "../../db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['applications_id'];
    $status = $_POST['status'];

    try {
        $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE applications_id = ?");
        $stmt->execute([$status, $applications_id]);
        
        header("Location: AApplications.php?success=Application status updated");
        exit();
    } catch (PDOException $e) {
        header("Location: AApplications.php?error=Failed to update status");
        exit();
    }
} else {
    header("Location: AApplications.php");
    exit();
}
?>
