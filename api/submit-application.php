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
$data = json_decode(file_get_contents('php://input'), true);

try {
    // Validate required fields
    if (empty($data['opportunity_id']) || empty($data['application_text'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit();
    }

    // Insert new application
    $stmt = $conn->prepare("
        INSERT INTO applications 
        (student_id, opportunity_id, application_text, status, submitted_at)
        VALUES (?, ?, ?, 'Pending', NOW())
    ");
    $stmt->execute([
        $student_id,
        $data['opportunity_id'],
        $data['application_text']
    ]);

    // Get the new application ID
    $application_id = $conn->lastInsertId();

    // Return success response
    echo json_encode([
        "success" => true,
        "message" => "Application submitted successfully",
        "application_id" => $application_id
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit();
}
?>
