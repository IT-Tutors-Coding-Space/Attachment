<?php
require_once "../db.php";

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../../SignUps/Slogin.php");
    exit();
}

$student_id = $_SESSION["user_id"];

try {
    // Validate required fields
    if (!isset($_POST['opportunities_id'])) {
        die("Error: Opportunity ID is required");
    }
    
    $opportunity_id = (int)$_POST['opportunities_id'];
    
    if ($opportunity_id <= 0) {
        die("Error: Invalid opportunity ID");
    }

    // Verify opportunity exists
    $stmt = $conn->prepare("SELECT 1 FROM opportunities WHERE opportunities_id = ?");
    $stmt->execute([$opportunity_id]);
    if (!$stmt->fetch()) {
        die("Error: Invalid opportunity ID");
    }

    // Validate file uploads
    $errors = [];
    
    if (empty($_FILES['cover_letter']['name'])) {
        $errors[] = "Cover letter is required";
    }
    
    if (empty($_FILES['resume']['name'])) {
        $errors[] = "Resume is required";
    }
    
    if (!empty($errors)) {
        die("Error: " . implode(", ", $errors));
    }

    // Process cover letter
    $cover_letter = $_FILES['cover_letter'];
    $cover_content = file_get_contents($cover_letter['tmp_name']);
    if ($cover_content === false) {
        die("Error: Failed to read cover letter file");
    }

    // Process resume
    $resume = $_FILES['resume'];
    $resume_content = file_get_contents($resume['tmp_name']);
    if ($resume_content === false) {
        die("Error: Failed to read resume file");
    }

    // Insert application
    $stmt = $conn->prepare("
        INSERT INTO applications 
        (student_id, opportunities_id, cover_letter, resume, status, submitted_at)
        VALUES (?, ?, ?, ?, 'Pending', NOW())
    ");
    
    $stmt->execute([
        $student_id,
        $opportunity_id,
        $cover_content,
        $resume_content
    ]);

    // Set success message in session
    $_SESSION['application_success'] = "Application submitted successfully!";
    
    // Redirect back to browse page
    header("Location: ../Pages/Students/SBrowse.php");
    exit();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
