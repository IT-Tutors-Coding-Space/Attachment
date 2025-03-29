<?php
require_once "../db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$student_id = $_SESSION["user_id"];

try {
    // Fetch total applications
    $totalApplicationsStmt = $conn->prepare("SELECT COUNT(*) AS total FROM applications WHERE student_id = ?");
    $totalApplicationsStmt->execute([$student_id]);
    $totalApplications = $totalApplicationsStmt->fetch(PDO::FETCH_ASSOC)["total"];

    // Fetch accepted applications
    $acceptedApplicationsStmt = $conn->prepare("SELECT COUNT(*) AS total FROM applications WHERE student_id = ? AND status = 'Accepted'");
    $acceptedApplicationsStmt->execute([$student_id]);
    $acceptedApplications = $acceptedApplicationsStmt->fetch(PDO::FETCH_ASSOC)["total"];

    // Fetch pending applications
    $pendingApplicationsStmt = $conn->prepare("SELECT COUNT(*) AS total FROM applications WHERE student_id = ? AND status = 'Pending'");
    $pendingApplicationsStmt->execute([$student_id]);
    $pendingApplications = $pendingApplicationsStmt->fetch(PDO::FETCH_ASSOC)["total"];

    // Fetch recent applications
    $recentApplicationsStmt = $conn->prepare("SELECT opportunity_title, company_name, status FROM applications WHERE student_id = ? ORDER BY created_at DESC LIMIT 5");
    $recentApplicationsStmt->execute([$student_id]);
    $recentApplications = $recentApplicationsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Return data as JSON
    echo json_encode([
        "totalApplications" => $totalApplications,
        "acceptedApplications" => $acceptedApplications,
        "pendingApplications" => $pendingApplications,
        "recentApplications" => $recentApplications
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
