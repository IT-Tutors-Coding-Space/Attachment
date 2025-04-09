<?php
require_once "../../db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: ../../SignUps/CLogin.php");
    exit();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$company_id = $_SESSION["user_id"];
$filter = isset($_GET['filter']) ? filter_var($_GET['filter'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : 'all';

try {
    // Fetch company name
    $companyStmt = $conn->prepare("SELECT company_name FROM companies WHERE company_id = ?");
    $companyStmt->execute([$company_id]);
    $company = $companyStmt->fetch(PDO::FETCH_ASSOC);
    $company_name = $company ? htmlspecialchars($company["company_name"]) : "Unknown Company";

    // Handle status update
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
        // CSRF validation
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'CSRF token validation failed']);
                exit();
            } else {
                $_SESSION['message'] = "CSRF token validation failed";
                $_SESSION['message_type'] = "danger";
                header("Location: CTrack.php");
                exit();
            }
        }

        $application_id = $_POST["application_id"];
        $new_status = $_POST["status"];

        // Verify application belongs to company
        $verifyStmt = $conn->prepare("SELECT opportunities.company_id 
                                    FROM applications 
                                    JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
                                    WHERE applications.applications_id = ?");
        $verifyStmt->execute([$application_id]);
        $appOwner = $verifyStmt->fetch(PDO::FETCH_ASSOC);

        if (!$appOwner || $appOwner['company_id'] != $company_id) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'No application found or you don\'t have permission to update it']);
                exit();
            } else {
                $_SESSION['message'] = "No application found or you don't have permission to update it";
                $_SESSION['message_type'] = "danger";
                header("Location: CTrack.php");
                exit();
            }
        }

        $updateStmt = $conn->prepare("UPDATE applications SET status = ? WHERE applications_id = ?");
        $updateStmt->execute([$new_status, $application_id]);

        if ($updateStmt->rowCount() > 0) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Status updated successfully!']);
                exit();
            } else {
                $_SESSION['message'] = "Status updated successfully!";
                $_SESSION['message_type'] = "success";
                header("Location: CTrack.php");
                exit();
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'No changes made to the application status']);
                exit();
            } else {
                $_SESSION['message'] = "No changes made to the application status";
                $_SESSION['message_type'] = "info";
                header("Location: CTrack.php");
                exit();
            }
        }
    }

    // Handle file downloads
    if (isset($_GET['action']) && $_GET['action'] === 'download' && isset($_GET['type']) && isset($_GET['id'])) {
        $application_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        $file_type = filter_var($_GET['type'], FILTER_SANITIZE_STRING);

        // Verify application belongs to company
        $verifyStmt = $conn->prepare("SELECT opportunities.company_id, applications.resume, applications.cover_letter 
                                    FROM applications 
                                    JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
                                    WHERE applications.applications_id = ?");
        $verifyStmt->execute([$application_id]);
        $appData = $verifyStmt->fetch(PDO::FETCH_ASSOC);

        if (!$appData || $appData['company_id'] != $company_id) {
            $_SESSION['message'] = "No application found or you don't have permission to access it";
            $_SESSION['message_type'] = "danger";
            header("Location: CTrack.php");
            exit();
        }

        $file_path = null;
        $file_name = '';

        if ($file_type === 'resume' && !empty($appData['resume'])) {
            $file_path = "../../uploads/resumes/" . $appData['resume'];
            $file_name = "Resume_" . basename($appData['resume']);
        } elseif ($file_type === 'cover' && !empty($appData['cover_letter'])) {
            $file_path = "../../uploads/cover_letters/" . $appData['cover_letter'];
            $file_name = "CoverLetter_" . basename($appData['cover_letter']);
        }

        if ($file_path && file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit();
        } else {
            $_SESSION['message'] = "File not found or unavailable";
            $_SESSION['message_type'] = "danger";
            header("Location: CTrack.php");
            exit();
        }
    }

    // Function to get application details for view modal
    function getApplicationDetails($conn, $application_id, $company_id)
    {
        try {
            $stmt = $conn->prepare("SELECT 
                                    applications.*, 
                                    students.full_name, 
                                    students.email, 
                                    students.phone, 
                                    students.university, 
                                    students.major, 
                                    students.graduation_year,
                                    opportunities.title,
                                    opportunities.description
                                FROM applications 
                                JOIN students ON applications.student_id = students.student_id
                                JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
                                WHERE applications.applications_id = ? AND opportunities.company_id = ?");
            $stmt->execute([$application_id, $company_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Check if viewing application details
    $viewing_application = false;
    $application_details = null;
    if (isset($_GET['view']) && is_numeric($_GET['view'])) {
        $application_id = intval($_GET['view']);
        $application_details = getApplicationDetails($conn, $application_id, $company_id);
        if ($application_details) {
            $viewing_application = true;
        } else {
            $_SESSION['message'] = "Application not found or you don't have permission to view it";
            $_SESSION['message_type'] = "danger";
            header("Location: CTrack.php");
            exit();
        }
    }

    // Build base query for applications table
    $sql = "SELECT applications.*, students.full_name, opportunities.title 
            FROM applications 
            JOIN students ON applications.student_id = students.student_id
            JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
            WHERE opportunities.company_id = ?";

    // Add filter condition
    $params = [$company_id];
    if ($filter !== 'all') {
        $sql .= " AND applications.status = ?";
        $params[] = $filter;
    }

    // Add sorting
    $sql .= " ORDER BY applications.submitted_at DESC";

    // Fetch applications
    $applicationsStmt = $conn->prepare($sql);
    $applicationsStmt->execute($params);
    $applications = $applicationsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['message'] = "Error fetching data: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    header("Location: CTrack.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Tracking - AttachME</title>
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../CSS/CTrack.css">
    <style>
        .status-filter {
            margin-bottom: 20px;
        }

        .status-badge {
            font-size: 0.9rem;
            padding: 0.35em 0.65em;
        }

        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .application-details dt {
            font-weight: 600;
            color: #495057;
        }

        .application-details dd {
            margin-bottom: 1rem;
            color: #212529;
        }

        .file-download-btn {
            width: 100%;
            margin-bottom: 10px;
        }

        .modal-backdrop.show {
            opacity: 0.5;
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-white" href="CHome.php">AttachME - Opportunities</a>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="CHome.php" class="nav-link text-white fw-bold fs-5">Dashboard</a></li>
                <li class="nav-item"><a href="COpportunities.php"
                        class="nav-link text-white fw-bold fs-5">Opportunities</a></li>
                <li class="nav-item"><a href="CTrack.php"
                        class="nav-link text-white fw-bold fs-5 active">Applications</a></li>
                <li class="nav-item"><a href="CNotifications.php" class="nav-link text-white fw-bold fs-5">Messages</a>
                </li>
                <li class="nav-item"><a href="CProfile.php" class="nav-link text-white fw-bold fs-5">Profile</a></li>
            </ul>
        </div>
    </nav>

    <!-- Application Details Modal -->
    <?php if ($viewing_application): ?>
        <div class="modal fade show" id="applicationModal" tabindex="-1" aria-labelledby="applicationModalLabel"
            aria-modal="true" role="dialog" style="display: block; padding-right: 17px;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="applicationModalLabel">Application Details</h5>
                        <a href="CTrack.php" class="btn-close btn-close-white" aria-label="Close"></a>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="mb-4"><?= htmlspecialchars($application_details['full_name']) ?>'s Application
                                </h4>

                                <dl class="row application-details">
                                    <dt class="col-sm-4">Opportunity:</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($application_details['title']) ?></dd>

                                    <dt class="col-sm-4">Application Date:</dt>
                                    <dd class="col-sm-8">
                                        <?= date('M j, Y \a\t g:i a', strtotime($application_details['submitted_at'])) ?>
                                    </dd>

                                    <dt class="col-sm-4">Status:</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge status-badge bg-<?=
                                            $application_details['status'] === 'Accepted' ? 'success' :
                                            ($application_details['status'] === 'Rejected' ? 'danger' : 'warning') ?>">
                                            <?= htmlspecialchars($application_details['status']) ?>
                                        </span>
                                    </dd>

                                    <dt class="col-sm-4">Email:</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($application_details['email']) ?></dd>

                                    <dt class="col-sm-4">Phone:</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($application_details['phone']) ?></dd>

                                    <dt class="col-sm-4">University:</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($application_details['university']) ?></dd>

                                    <dt class="col-sm-4">Major:</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($application_details['major']) ?></dd>

                                    <dt class="col-sm-4">Graduation Year:</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($application_details['graduation_year']) ?>
                                    </dd>

                                    <dt class="col-sm-4">Additional Information:</dt>
                                    <dd class="col-sm-8">
                                        <?= !empty($application_details['additional_info']) ? nl2br(htmlspecialchars($application_details['additional_info'])) : 'None provided' ?>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Documents</h5>
                                    </div>
                                    <div class="card-body text-center">
                                        <?php if (!empty($application_details['resume'])): ?>
                                            <a href="CTrack.php?action=download&type=resume&id=<?= $application_details['applications_id'] ?>"
                                                class="btn btn-primary file-download-btn">
                                                <i class="fas fa-file-pdf me-2"></i> Download Resume
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary file-download-btn" disabled>
                                                <i class="fas fa-file-pdf me-2"></i> No Resume Uploaded
                                            </button>
                                        <?php endif; ?>

                                        <?php if (!empty($application_details['cover_letter'])): ?>
                                            <a href="CTrack.php?action=download&type=cover&id=<?= $application_details['applications_id'] ?>"
                                                class="btn btn-primary file-download-btn">
                                                <i class="fas fa-file-pdf me-2"></i> Download Cover Letter
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary file-download-btn" disabled>
                                                <i class="fas fa-file-pdf me-2"></i> No Cover Letter Uploaded
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Update Status</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="CTrack.php" class="update-status-form">
                                            <input type="hidden" name="application_id"
                                                value="<?= $application_details['applications_id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <div class="d-grid gap-2">
                                                <input type="hidden" name="status" value="Accepted">
                                                <button type="submit" name="update_status"
                                                    class="btn btn-success <?= $application_details['status'] === 'Accepted' ? 'disabled' : '' ?>">
                                                    <i class="fas fa-check me-1"></i> Accept Application
                                                </button>

                                                <input type="hidden" name="status" value="Rejected">
                                                <button type="submit" name="update_status"
                                                    class="btn btn-danger <?= $application_details['status'] === 'Rejected' ? 'disabled' : '' ?>">
                                                    <i class="fas fa-times me-1"></i> Reject Application
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="CTrack.php" class="btn btn-secondary">Close</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    <?php endif; ?>

    <div class="container p-5 flex-grow-1">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show mb-4">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']);
            unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">Applications Tracking</h2>

            <!-- Status Filter -->
            <div class="btn-group status-filter">
                <a href="CTrack.php?filter=all"
                    class="btn btn-outline-secondary <?= $filter === 'all' ? 'active' : '' ?>">All</a>
                <a href="CTrack.php?filter=Pending"
                    class="btn btn-outline-warning <?= $filter === 'Pending' ? 'active' : '' ?>">Pending</a>
                <a href="CTrack.php?filter=Accepted"
                    class="btn btn-outline-success <?= $filter === 'Accepted' ? 'active' : '' ?>">Accepted</a>
                <a href="CTrack.php?filter=Rejected"
                    class="btn btn-outline-danger <?= $filter === 'Rejected' ? 'active' : '' ?>">Rejected</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <?php if (count($applications) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Opportunity</th>
                                    <th>Application Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                    <tr data-application-id="<?= $app['applications_id'] ?>">
                                        <td><?= htmlspecialchars($app['full_name']) ?></td>
                                        <td><?= htmlspecialchars($app['title']) ?></td>
                                        <td><?= date('M j, Y', strtotime($app['submitted_at'])) ?></td>
                                        <td>
                                            <span class="badge status-badge bg-<?=
                                                $app['status'] === 'Accepted' ? 'success' :
                                                ($app['status'] === 'Rejected' ? 'danger' : 'warning') ?>">
                                                <?= htmlspecialchars($app['status']) ?>
                                            </span>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="CTrack.php?view=<?= $app['applications_id'] ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                            <form method="POST" action="CTrack.php" class="d-inline update-status-form">
                                                <input type="hidden" name="application_id"
                                                    value="<?= $app['applications_id'] ?>">
                                                <input type="hidden" name="status" value="Accepted">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                <button type="submit" name="update_status"
                                                    class="btn btn-sm btn-outline-success <?= $app['status'] === 'Accepted' ? 'disabled' : '' ?>">
                                                    <i class="fas fa-check me-1"></i> Accept
                                                </button>
                                            </form>
                                            <form method="POST" action="CTrack.php" class="d-inline update-status-form">
                                                <input type="hidden" name="application_id"
                                                    value="<?= $app['applications_id'] ?>">
                                                <input type="hidden" name="status" value="Rejected">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                <button type="submit" name="update_status"
                                                    class="btn btn-sm btn-outline-danger <?= $app['status'] === 'Rejected' ? 'disabled' : '' ?>">
                                                    <i class="fas fa-times me-1"></i> Reject
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No applications found</h5>
                        <p class="text-muted">There are no applications matching your selected filter</p>
                        <a href="COpportunities.php" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-1"></i> Post New Opportunity
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../Javascript/CTrack.js"></script>
</body>

</html>