<?php
require_once "../db.php";
header("Content-Type: application/json");

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$student_id = $_SESSION["user_id"];

try {
    // Detailed debug logging
    error_log("=== START APPLICATION SUBMISSION ===");
    error_log("Session user_id: " . ($_SESSION["user_id"] ?? 'NULL'));
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    error_log("Raw input: " . file_get_contents('php://input'));

    // Validate opportunity ID
    if (!isset($_POST['opportunities_id']) && !isset($_POST['opportunities_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Opportunity ID is required"]);
        exit();
    }
    
    // Handle both field name variations
    $opportunities_id = isset($_POST['opportunities_id']) ? $_POST['opportunities_id'] : $_POST['opportunities_id'];
    
    if (!is_numeric($opportunity_id)) {
        http_response_code(400);
        echo json_encode(["error" => "Opportunity ID must be a number"]);
        exit();
    }
    $opportunities_id = (int)$opportunities_id;
    
    // Verify opportunity exists
    $stmt = $conn->prepare("SELECT 1 FROM opportunities WHERE opportunities_id = ?");
    $stmt->execute([$opportunities_id]);
    if (!$stmt->fetch()) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid opportunity ID"]);
        exit();
    }

    // Handle file upload
    if (empty($_FILES['cover_letter']['name'])) {
        http_response_code(400);
        echo json_encode(["error" => "Cover letter PDF is required"]);
        exit();
    }

    $cover_letter = $_FILES['cover_letter'];
    $upload_dir = "../uploads/cover_letters/";
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Read PDF file content
    $pdf_content = file_get_contents($cover_letter['tmp_name']);
    if ($pdf_content === false) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to read cover letter file"]);
        exit();
    }

    // Validate PDF file
    $finfo = new finfo(FILEINFO_MIME);
    $mime = $finfo->file($cover_letter['tmp_name']);
    if (strpos($mime, 'pdf') === false) {
        http_response_code(400);
        echo json_encode(["error" => "Only PDF files are allowed for cover letter"]);
        exit();
    }

    // Debug log input values
    error_log("Submitting application with values:");
    error_log("Student ID: " . $student_id);
    error_log("Opportunity ID: " . $_POST['opportunities_id']);
    error_log("Cover Letter Filename: " . $file_name);

    // Insert new application with NULLIF protection
    $stmt = $conn->prepare("
        INSERT INTO applications 
        (student_id, opportunities_id, cover_letter, status, submitted_at)
        VALUES (?, NULLIF(?, ''), ?, 'Pending', NOW())
    ");
    
    // Final null check with detailed logging
    if (empty($opportunities_id)) {
        error_log("CRITICAL: Opportunity ID is null/empty before execution");
        error_log("Validated opportunity_id: " . var_export($opportunity_id, true));
        error_log("POST data: " . print_r($_POST, true));
        http_response_code(400);
        echo json_encode(["error" => "System error: Opportunity ID missing"]);
        exit();
    }

    $params = [
        $student_id,
        $opportunities_id,
        $pdf_content
    ];
    
    error_log("Final execution params: " . print_r($params, true));
    
    try {
        $conn->beginTransaction();
        $stmt->execute($params);
        $conn->commit();
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Database error: " . $e->getMessage());
        throw $e;
    }
    
    error_log("Inserted application ID: " . $conn->lastInsertId());

    // Return success response
    echo json_encode([
        "success" => true,
        "message" => "Application submitted successfully",
        "applications_id" => $conn->lastInsertId()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit();
}
?>
