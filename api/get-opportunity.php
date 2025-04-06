<?php
require_once "../db.php";
header("Content-Type: application/json");

$opportunity_id = $_GET['id'] ?? null;

if (!$opportunity_id) {
    http_response_code(400);
    echo json_encode(["error" => "Opportunity ID is required"]);
    exit();
}

try {
    $stmt = $conn->prepare("
        SELECT o.*, c.company_name 
        FROM opportunities o
        JOIN companies c ON o.company_id = c.company_id
        WHERE o.opportunities_id = ?
    ");
    $stmt->execute([$opportunity_id]);
    $opportunity = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$opportunity) {
        http_response_code(404);
        echo json_encode(["error" => "Opportunity not found"]);
        exit();
    }

    echo json_encode($opportunity);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit();
}
?>
