<?php
require_once "../../db.php";

if (!isset($_GET['id'])) {
    die("Application ID not provided");
}

$applicationId = $_GET['id'];

try {
    // Fetch application details with all required fields
    $stmt = $conn->prepare("
        SELECT a.*, s.full_name, s.email,
               o.title AS opportunity_title, c.company_name, c.location AS company_location
        FROM applications a
        JOIN students s ON a.student_id = s.student_id
        JOIN opportunities o ON a.opportunities_id = o.opportunities_id
        JOIN companies c ON o.company_id = c.company_id
        WHERE a.applications_id = ?
    ");
    $stmt->execute([$applicationId]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
        die("Application not found");
    }

    // Handle document viewing requests
    if (isset($_GET['view'])) {
        $type = $_GET['view'];
        if ($type === 'cover_letter' && !empty($application['cover_letter'])) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="cover_letter_'.$applicationId.'.pdf"');
            echo $application['cover_letter'];
            exit();
        } elseif ($type === 'resume' && !empty($application['resume'])) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="resume_'.$applicationId.'.pdf"');
            echo $application['resume'];
            exit();
        }
    }

    // Regular HTML display
    header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .detail-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .detail-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            border-radius: 10px 10px 0 0;
        }
        .detail-body {
            padding: 20px;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            margin-bottom: 15px;
        }
        .document-btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .document-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="detail-card">
            <div class="detail-header">
                <h4>Application Details</h4>
            </div>
            <div class="detail-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-label">Student Name</div>
                        <div class="detail-value"><?= htmlspecialchars($application['full_name']) ?></div>
                        
                        <div class="detail-label">Email</div>
                        <div class="detail-value"><?= htmlspecialchars($application['email']) ?></div>
                        
                      
                    </div>
                    <div class="col-md-6">
                        <div class="detail-label">Opportunity</div>
                        <div class="detail-value"><?= htmlspecialchars($application['opportunity_title']) ?></div>
                        
                        <div class="detail-label">Company</div>
                        <div class="detail-value"><?= htmlspecialchars($application['company_name']) ?></div>
                        
                        <div class="detail-label">Location</div>
                        <div class="detail-value"><?= htmlspecialchars($application['company_location']) ?></div>
                    </div>
                </div>
                
                <div class="detail-label">Application Date</div>
                <div class="detail-value"><?= htmlspecialchars($application['submitted_at']) ?></div>
                
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    <span class="badge bg-<?= 
                        $application['status'] == 'Accepted' ? 'success' : 
                        ($application['status'] == 'Rejected' ? 'danger' : 'warning') 
                    ?>">
                        <?= htmlspecialchars($application['status']) ?>
                    </span>
                </div>
                
                <div class="document-section">
                    <div class="detail-label">Documents</div>
                    <div class="d-flex flex-wrap">
                        <?php if (!empty($application['cover_letter'])): ?>
                        <a href="get-application-details.php?id=<?= $applicationId ?>&view=cover_letter" 
                           class="btn btn-outline-primary document-btn" target="_blank">
                            View Cover Letter
                        </a>
                        <?php else: ?>
                        <button class="btn btn-outline-secondary document-btn" disabled>No Cover Letter</button>
                        <?php endif; ?>
                        
                        <?php if (!empty($application['resume'])): ?>
                        <a href="get-application-details.php?id=<?= $applicationId ?>&view=resume" 
                           class="btn btn-outline-primary document-btn" target="_blank">
                            View Resume
                        </a>
                        <?php else: ?>
                        <button class="btn btn-outline-secondary document-btn" disabled>No Resume</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
