<?php
require_once "../db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = $_POST["full_name"];
    $email = $_POST["email"];
    $gender = $_POST["gender"];
    $registrationNumber = $_POST["registration_number"];
    $level = $_POST["level"];
    $yearOfStudy = $_POST["year_of_study"];
    $course = $_POST["course"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, gender, registration_number, level, year_of_study, course, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)" );
        $stmt->execute([$fullName, $email, $gender, $registrationNumber, $level, $yearOfStudy, $course, $password]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
