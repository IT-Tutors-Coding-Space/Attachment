<?php
header('Content-Type: application/json');
require "../../db.php";

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit();
}

// Validate required fields
$required = ['user_id', 'recipient_id', 'is_typing'];
foreach ($required as $field) {
    if (!isset($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: $field"]);
        exit();
    }
}

try {
    // Check if typing record exists
    $stmt = $conn->prepare("
        SELECT id FROM typing_indicators 
        WHERE user_id = ? AND recipient_id = ?
    ");
    $stmt->execute([$input['user_id'], $input['recipient_id']]);
    $exists = $stmt->fetch();

    if ($exists) {
        // Update existing record
        $stmt = $conn->prepare("
            UPDATE typing_indicators 
            SET is_typing = ?, last_updated = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$input['is_typing'], $exists['id']]);
    } else {
        // Create new record
        $stmt = $conn->prepare("
            INSERT INTO typing_indicators 
            (user_id, recipient_id, is_typing, last_updated)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([
            $input['user_id'], 
            $input['recipient_id'], 
            $input['is_typing']
        ]);
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
