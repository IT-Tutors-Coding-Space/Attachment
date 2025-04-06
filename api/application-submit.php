<?php
require_once "../db.php";
session_start();

// Check and add application_link column if missing
try {
    $stmt = $conn->query("PRAGMA table_info(opportunities)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasApplicationLink = false;
    
    foreach ($columns as $col) {
        if ($col['name'] === 'application_link') {
            $hasApplicationLink = true;
            break;
        }
    }
    
    if (!$hasApplicationLink) {
        $conn->exec("ALTER TABLE opportunities ADD COLUMN application_link VARCHAR(255)");
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database configuration error"]);
    exit();
}

// Validate user is logged in as student
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}

// Get input data
$data = json_decode(file_get_contents("php://input"), true);
$student_id = $_SESSION["user_id"];
$opportunity_id = $data["opportunities_id"] ?? null;

// Validate input
if (!$opportunity_id) {
    http_response_code(400);
    echo json_encode(["error" => "Opportunity ID is required"]);
    exit();
}

try {
    // Check if application already exists
    $checkStmt = $conn->prepare("SELECT applications_id FROM applications WHERE student_id = ? AND opportunities_id = ?");
    $checkStmt->execute([$student_id, $opportunity_id]);
    
    if ($checkStmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Application already exists"]);
        exit();
    }

    // Insert new application
    $insertStmt = $conn->prepare("INSERT INTO applications (student_id, opportunities_id, status) VALUES (?, ?, 'Pending')");
    $insertStmt->execute([$student_id, $opportunity_id]);
    
    // Get company name for response
    $companyStmt = $conn->prepare("
        SELECT c.company_name, o.title 
        FROM opportunities o
        JOIN companies c ON o.company_id = c.company_id
        WHERE o.opportunities_id = ?
    ");
    $companyStmt->execute([$opportunity_id]);
    $opportunity = $companyStmt->fetch(PDO::FETCH_ASSOC);

    http_response_code(201);
    echo json_encode([
        "message" => "Application submitted successfully",
        "opportunity" => $opportunity["title"],
        "company" => $opportunity["company_name"]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
