<?php
session_start();
require "../db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $companyName = $_POST["company_name"]; 
    $email = $_POST["contact_email"];
    $location = $_POST["location"];
    $industry = $_POST["industry"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    try {
        $stmt = $conn->prepare("INSERT INTO companies (company_name, contact_email, location, industry, password_hash, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$companyName, $email, $location, $industry, $password]);
        
        // Redirect to login page after successful registration
        header("Location: Loginn.php");
        exit();
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
