<?php
// Ensure no output before headers
if (ob_get_level()) ob_end_clean();

require_once '../../db.php';
session_start();

// Explicitly set headers first
header('Content-Type: application/json');

// Check if user is logged in as company
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access'], JSON_UNESCAPED_SLASHES));
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Application ID required'], JSON_UNESCAPED_SLASHES));
}

$application_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$company_id = $_SESSION["user_id"];

try {
    // Verify the application belongs to this company
    $stmt = $conn->prepare("
        SELECT a.cover_letter, a.resume, s.full_name, s.email, s.course, s.year_of_study, o.title
        FROM applications a
        JOIN opportunities o ON a.opportunities_id = o.opportunities_id
        JOIN students s ON a.student_id = s.student_id
        WHERE a.applications_id = ? AND o.company_id = ?
    ");
    $stmt->execute([$application_id, $company_id]);
    
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$application) {
        http_response_code(404);
        exit(json_encode(['error' => 'Application not found or unauthorized'], JSON_UNESCAPED_SLASHES));
    }

    // Return the application documents and student info
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'cover_letter' => $application['cover_letter'],
        'resume' => $application['resume'],
        'student_name' => $application['full_name'],
        'student_email' => $application['email'],
        'student_course' => $application['course'],
        'student_year' => $application['year_of_study'],
        'opportunity_title' => $application['title']
    ], JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()], JSON_UNESCAPED_SLASHES);
}
