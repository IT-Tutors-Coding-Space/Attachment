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
    // Get application count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM applications WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $applicationCount = $stmt->fetchColumn();

    // Get upcoming deadlines
    $stmt = $conn->prepare("
        SELECT o.title, o.application_deadline 
        FROM opportunities o
        JOIN applications a ON o.opportunities_id = a.opportunity_id
        WHERE a.student_id = ? AND o.application_deadline > NOW()
        ORDER BY o.application_deadline ASC
        LIMIT 5
    ");
    $stmt->execute([$student_id]);
    $upcomingDeadlines = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get recent notifications
    $stmt = $conn->prepare("
        SELECT * FROM notifications 
        WHERE student_id = ?
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$student_id]);
    $recentNotifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'applicationCount' => $applicationCount,
        'upcomingDeadlines' => $upcomingDeadlines,
        'recentNotifications' => $recentNotifications
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit();
}
?>
