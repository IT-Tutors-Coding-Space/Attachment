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

// Validate POST data
if (!isset($_POST["opportunities_id"]) || !isset($_POST["cover_letter"])) {
    http_response_code(400);
    exit(json_encode(["error" => "Missing required fields"]));
}

$opportunities_id = $_POST["opportunities_id"];
$cover_letter = $_POST["cover_letter"];

if (empty($opportunities_id) || empty($cover_letter)) {
    http_response_code(400);
    exit(json_encode(["error" => "Opportunity ID and cover letter cannot be empty"]));
}

// Validate file upload
if (!isset($_FILES["resume"]) || $_FILES["resume"]["error"] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    exit(json_encode(["error" => "Resume upload required"]));
}

$resume = $_FILES["resume"];
$allowed_types = ["application/pdf"];
$max_size = 5 * 1024 * 1024; // 5MB

if (!in_array($resume["type"], $allowed_types) || $resume["size"] > $max_size) {
    http_response_code(400);
    exit("Invalid resume file. Only PDFs under 5MB are allowed");
}

// Read resume file contents
$resume_data = file_get_contents($resume["tmp_name"]);
if ($resume_data === false) {
    http_response_code(500);
    exit("Error reading resume file");
}

try {
    // Insert application into database
    $stmt = $conn->prepare("INSERT INTO applications 
                          (student_id, opportunities_id, cover_letter, resume, status, submitted_at) 
                          VALUES (?, ?, ?, ?, 'Pending', NOW())");
    $stmt->execute([$student_id, $opportunities_id, $cover_letter, $resume_data]);
    
    http_response_code(201);
    echo "Application submitted successfully!";
} catch (PDOException $e) {
    http_response_code(500);
    echo "Error submitting application: " . $e->getMessage();
}
?>
