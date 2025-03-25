<?php
require_once "../db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    try {
        $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["role"] = $user["role"];

            echo json_encode(["success" => true, "message" => "Login successful"]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid email or password"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
