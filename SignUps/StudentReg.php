<?php
session_start();
require "../db.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input fields
    if (empty($_POST["full_name"]) || empty($_POST["email"]) || empty($_POST["level"]) || empty($_POST["gender"]) || empty($_POST["registration_number"]) || empty($_POST["year_of_study"]) || empty($_POST["course"]) || empty($_POST["password"]) || empty($_POST["confirm_password"])) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    // Validate email format
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email format"]);
        exit();
    }

    // Validate password confirmation
    if ($_POST["password"] !== $_POST["confirm_password"]) {
        echo "Passwords do not match.";
        exit();
    }

    // Check for duplicate email
    $email = $_POST["email"];
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(["success" => false, "message" => "Email is already registered."]);
        exit();
    }

    $fullName = $_POST["full_name"];
    $email = $_POST["email"];
    $level = $_POST["level"];
    $gender = $_POST["gender"];
    $registrationNumber = $_POST["registration_number"];
    $yearOfStudy = $_POST["year_of_study"];
    $course = $_POST["course"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("INSERT INTO students (full_name, email, level, password_hash, gender, registration_number, year_of_study, course, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

        $stmt->execute([$fullName, $email, $level, $password, $gender, $registrationNumber, $yearOfStudy, $course]);
        $user_id = $conn->lastInsertId(); // Get the newly inserted student_id

        $_SESSION["user_id"] = $user_id;
        $_SESSION["role"] = "Student";

        // Include the student_id in the users table
        $stmt = $conn->prepare("INSERT INTO users (student_id, email, password_hash, role, created_at) VALUES (?, ?, ?, 'student', NOW())");
        $stmt->execute([$user_id, $email, $password]);

        $conn->commit();

        // Redirect to login page after successful registration
        header("Location: Loginn.php");
        exit();
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        exit();
    }
}
?>
