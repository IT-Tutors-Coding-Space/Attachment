<?php
require_once "../db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        $email = $_POST["email"];
        $password = $_POST["password"];

        try {
            $stmt = $conn->prepare("SELECT * FROM companies WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["password_hash"])) {
                $_SESSION["user_id"] = $user["company_id"];
                $_SESSION["role"] = "company";

                header("Location: ../Pages/Company/CHome.php");
                exit();
            } else {
                $error = "Invalid email or password. Please check your credentials.";
            }
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            $error = "A server error occurred. Please try again later.";
        }
    }
}
?>
<!-- ...existing HTML code for login form... -->
