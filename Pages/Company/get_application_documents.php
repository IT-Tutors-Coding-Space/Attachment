<?php
require_once('../../db.php');
session_start();

// Check if user is logged in as company
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Application ID required']);
    exit();
}

$application_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$company_id = $_SESSION["user_id"];

try {
    // Verify the application belongs to this company's opportunity
    $stmt = $conn->prepare("
        SELECT 
            a.cover_letter, a.resume,
            s.student_id, s.full_name, s.email, s.course, s.year_of_study,
            o.title AS opportunity_title
        FROM applications a
        JOIN students s ON a.student_id = s.student_id
        JOIN opportunities o ON a.opportunities_id = o.opportunities_id
        WHERE a.applications_id = ? AND o.company_id = ?
    ");
    
    $stmt->execute([$application_id, $company_id]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
        http_response_code(404);
        echo json_encode(['error' => 'Application not found or unauthorized']);
        exit();
    }

    // Clean any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Prepare response data
    $response = [
        'cover_letter' => $application['cover_letter'] ?? null,
        'resume' => $application['resume'] ?? null,
        'student_name' => $application['full_name'] ?? '',
        'student_email' => $application['email'] ?? '',
        'student_course' => $application['course'] ?? '',
        'student_year' => $application['year_of_study'] ?? '',
        'opportunity_title' => $application['title'] ?? '',
        'is_pdf_cover_letter' => isset($application['cover_letter']) ? strpos($application['cover_letter'], '%PDF-') === 0 : false,
        'is_pdf_resume' => isset($application['resume']) ? strpos($application['resume'], '%PDF-') === 0 : false
    ];

    // Validate and encode JSON
    header('Content-Type: application/json');
    $json = json_encode($response);
    if ($json === false) {
        throw new Exception('JSON encoding failed: ' . json_last_error_msg());
    }
    echo $json;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
