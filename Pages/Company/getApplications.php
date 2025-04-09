<?php
session_start();
require_once '../../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

if (!isset($_SESSION['company_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Company not verified']);
    exit;
}

$company_id = $_SESSION['company_id'];

try {
    $query = "SELECT 
                app.id, 
                app.status, 
                app.application_date, 
                app.cover_letter,
                u.user_id as student_id,
                u.name as student_name,
                u.email as student_email,
                o.title as opportunity_title
              FROM applications app
              JOIN users u ON app.student_id = u.user_id
              JOIN opportunities o ON app.opportunity_id = o.id
              WHERE o.company_id = ?
              ORDER BY app.application_date DESC";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . print_r($conn->errorInfo(), true));
    }

    $stmt->bindValue(1, $company_id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . print_r($stmt->errorInfo(), true));
    }

    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ensure consistent status values
    foreach ($applications as &$app) {
        $app['status'] = strtolower($app['status']);
    }

    echo json_encode($applications);

} catch (PDOException $e) {
    error_log("Database error in getApplications: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} catch (Exception $e) {
    error_log("Error in getApplications: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'System error']);
}
?>