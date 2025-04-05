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
    $company_name = $_POST['company_name'];
    $email = $_POST['email'];
    $industry = $_POST['industry'];
    $location = $_POST['location'];
    $status = $_POST['status'];

    try {
        $stmt = $conn->prepare("UPDATE companies SET 
            company_name = :company_name,
            email = :email,
            industry = :industry,
            location = :location,
            status = :status
            WHERE company_id = :company_id");

        $stmt->bindParam(':company_name', $company_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':industry', $industry);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':company_id', $company_id);

        $stmt->execute();

        header("Location: ACompanies.php?success=Company updated successfully");
        exit();
    } catch (PDOException $e) {
        header("Location: ACompanies.php?error=Error updating company");
        exit();
    }
} else {
    header("Location: ACompanies.php");
    exit();
}
?>
