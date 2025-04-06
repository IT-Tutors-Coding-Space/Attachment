<?php
require_once "../db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed");
}

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    http_response_code(403);
    exit("Unauthorized");
}

$student_id = $_SESSION["user_id"];
$opportunity_id = $_POST["opportunity_id"] ?? null;
$cover_letter = $_POST["cover_letter"] ?? null;

// Validate file upload
if (!isset($_FILES["resume"]) || $_FILES["resume"]["error"] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    exit("Resume upload required");
}

$resume = $_FILES["resume"];
$allowed_types = ["application/pdf"];
$max_size = 5 * 1024 * 1024; // 5MB

if (!in_array($resume["type"], $allowed_types) || $resume["size"] > $max_size) {
    http_response_code(400);
    exit("Invalid resume file. Only PDFs under 5MB are allowed");
}

// Save resume to uploads directory
$upload_dir = "../uploads/resumes/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$resume_name = "resume_" . $student_id . "_" . time() . ".pdf";
$resume_path = $upload_dir . $resume_name;

if (!move_uploaded_file($resume["tmp_name"], $resume_path)) {
    http_response_code(500);
    exit("Error saving resume");
}

try {
    // Insert application into database
    $stmt = $conn->prepare("INSERT INTO applications 
                          (student_id, opportunities_id, cover_letter, resume_path, status, application_date) 
                          VALUES (?, ?, ?, ?, 'Pending', NOW())");
    $stmt->execute([$student_id, $opportunity_id, $cover_letter, $resume_path]);
    
    http_response_code(201);
    echo "Application submitted successfully!";
} catch (PDOException $e) {
    // Delete uploaded file if DB insert fails
    unlink($resume_path);
    http_response_code(500);
    echo "Error submitting application: " . $e->getMessage();
}
?>
