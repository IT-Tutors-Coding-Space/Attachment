<?php
session_start();
require "../db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = $_POST["full_name"];
    $email = $_POST["email"];
    $role = $_POST["role"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    try {
        $stmt = $conn->prepare("INSERT INTO admins (full_name, email, role, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$fullName, $email, $role, $password]);
        
        // Redirect to login page after successful registration
        header("Location: Loginn.php");
        exit();
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
