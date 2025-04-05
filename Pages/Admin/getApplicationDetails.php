<?php
require_once "../../db.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("SELECT a.*, s.*, o.title AS opportunity_title, 
                           c.name AS company_name 
                           FROM applications a
                           JOIN students u ON a.student_id = s.user_id
                           JOIN opportunities o ON a.opportunities_id = o.opportunities_id
                           JOIN companies c ON o.company_id = c.company_id
                           WHERE a.applications_id = ?");
    $stmt->execute([$id]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($application) {
        echo '<div class="row">
                <div class="col-md-6">
                    <h5>Student Information</h5>
                    <p><strong>Name:</strong> '.$application['full_name'].'</p>
                    <p><strong>Email:</strong> '.$application['email'].'</p>
                </div>
                <div class="col-md-6">
                    <h5>Application Details</h5>
                    <p><strong>Opportunity:</strong> '.$application['opportunity_title'].'</p>
                    <p><strong>Company:</strong> '.$application['company_name'].'</p>
                    <p><strong>Applied On:</strong> '.$application['submitted_at'].'</p>
                    <p><strong>Status:</strong> <span class="badge '.($application['status'] == 'Accepted' ? 'bg-success' : ($application['status'] == 'Rejected' ? 'bg-danger' : 'bg-warning')).'">'.$application['status'].'</span></p>
                </div>
              </div>
              <div class="mt-4">
                <h5>Cover Letter</h5>
                <div class="border p-3">'.nl2br($application['cover_letter'] ?? 'Not provided').'</div>
              </div>
              <div class="mt-3">
                <h5>Feedback</h5>
                <div class="border p-3">'.nl2br($application['feedback'] ?? 'No feedback yet').'</div>
              </div>';
    } else {
        echo '<div class="alert alert-danger">Application not found</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request</div>';
}
?>
