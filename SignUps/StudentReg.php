<?php
session_start();
require "../db.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = $_POST["full_name"];
    $email = $_POST["email"];
    $level = $_POST["level"];

    $gender = $_POST["gender"];
    $registrationNumber = $_POST["registration_number"];
    $yearOfStudy = $_POST["year_of_study"];
    $course = $_POST["course"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    try {
        $stmt = $conn->prepare("INSERT INTO students (full_name, email,level,password_hash, gender, registration_number,year_of_study,course,created_at,updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?,NOW() ,NOW())" );
        $stmt->execute([$fullName, $email, $level,$password, $gender,$registrationNumber, $yearOfStudy, $course]);
        echo json_encode(["success" => true]);

        // Redirect to login page after successful registration
        header("Location: Loginn.php");
        exit();
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);

            
            

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
