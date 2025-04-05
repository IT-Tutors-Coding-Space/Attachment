<?php
require_once "../../db.php";
session_start();

// Check if the user is logged in as admin
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: Alogin.php");
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_POST['company_id'];

    try {
        $stmt = $conn->prepare("DELETE FROM companies WHERE company_id = :company_id");
        $stmt->bindParam(':company_id', $company_id);
        $stmt->execute();

        header("Location: ACompanies.php?success=Company deleted successfully");
        exit();
    } catch (PDOException $e) {
        header("Location: ACompanies.php?error=Error deleting company");
        exit();
    }
} else {
    header("Location: ACompanies.php");
    exit();
}
?>
