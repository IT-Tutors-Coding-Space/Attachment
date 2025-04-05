<?php
require_once "../../db.php";
session_start();

// Check if the user is logged in as admin
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: Alogin.php");
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $opportunities_id = $_POST['opportunities_id'];

    try {
        $stmt = $conn->prepare("DELETE FROM opportunities WHERE opportunities_id = :opportunities_id");
        $stmt->bindParam(':opportunities_id', $opportunities_id);
        error_log("Deleting opportunity ID: $opportunities_id"); // Log the operation
        $stmt->execute();

        header("Location: AOpportunities.php?success=Opportunity deleted successfully");
        exit();
    } catch (PDOException $e) {
        header("Location: AOpportunities.php?error=Error deleting opportunity");
        exit();
    }
} else {
    header("Location: AOpportunities.php");
    exit();
}
?>
