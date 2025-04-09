<?php
require_once('../../db.php');
session_start();

// Check if user is logged in as company
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: ../../SignUps/Clogin.php");
    exit();
}

$company_id = $_SESSION["user_id"];
$message = '';
$messageType = '';

// Handle application status update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    try {
        $application_id = filter_input(INPUT_POST, 'application_id', FILTER_VALIDATE_INT);
        $new_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

        if (!$application_id || !$new_status) {
            throw new Exception("Invalid input data");
        }

        $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE applications_id = ? AND company_id = ?");
        $stmt->execute([$new_status, $application_id, $company_id]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("No application found or you don't have permission to update it");
        }

        $message = "Application status updated successfully!";
        $messageType = "success";
    } catch (Exception $e) {
        $message = "Error updating application status: " . $e->getMessage();
        $messageType = "danger";
    }
}

// Fetch all applications for this company's opportunities with joined data
try {
    $opportunity_filter = filter_input(INPUT_GET, 'opportunity', FILTER_VALIDATE_INT);

    $query = "
        SELECT 
            a.applications_id, a.status, a.submitted_at, a.cover_letter,
            s.student_id, s.full_name, s.email, s.course, s.year_of_study,
            o.opportunities_id, o.title AS opportunity_title,
            c.company_id, c.company_name
        FROM applications a
        JOIN students s ON a.student_id = s.student_id
        JOIN opportunities o ON a.opportunities_id = o.opportunities_id
        JOIN companies c ON o.company_id = c.company_id
        WHERE o.company_id = ?
    ";

    $params = [$company_id];

    if ($opportunity_filter) {
        $query .= " AND o.opportunities_id = ?";
        $params[] = $opportunity_filter;
    }

    $query .= " ORDER BY a.submitted_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch opportunities for filter dropdown
    $stmt = $conn->prepare("SELECT opportunities_id, title FROM opportunities WHERE company_id = ?");
    $stmt->execute([$company_id]);
    $opportunities = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message = "Error fetching data: " . $e->getMessage();
    $messageType = "danger";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Student Applications - AttachME</title>
    <!-- Bootstrap 5 CSS -->
=======
    <title>Application Tracking - AttachME</title>
>>>>>>> 62fb1e5bca71397aa7565d25f7a09ece2b361669
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/CTrack.css">
</head>

<body class="bg-gray-100 d-flex flex-column min-vh-100">
    <!-- Loading Overlay -->
    <div id="loadingOverlay">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-white" href="CHome.php">AttachME - Opportunities</a>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="CHome.php" class="nav-link text-white fw-bold fs-5"> Dashboard</a></li>
                <li class="nav-item"><a href="COpportunities.php" class="nav-link text-white fw-bold fs-5">
                        Opportunities</a></li>
                <li class="nav-item"><a href="CTrack.php" class="nav-link text-white fw-bold fs-5 active">
                        Applications</a></li>
                <li class="nav-item"><a href="CNotifications.php" class="nav-link text-white fw-bold fs-5">
                        Messages</a></li>
                <li class="nav-item"><a href="CProfile.php" class="nav-link text-white fw-bold fs-5"> Profile</a></li>
            </ul>
        </div>
    </nav>

    <div class="container p-5 flex-grow-1">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show mb-4"
                role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">Applications Tracking</h2>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                    <li><a class="dropdown-item" href="CTrack.php">All Applications</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <?php foreach ($opportunities as $opp): ?>
                        <li><a class="dropdown-item"
                                href="CTrack.php?opportunity=<?= $opp['opportunities_id'] ?>"><?= htmlspecialchars($opp['title']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
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
                                    <tr class="application-card">
                                        <td>
                                            <strong><?= htmlspecialchars($app['full_name']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($app['email']) ?></small><br>
                                            <small><?= htmlspecialchars($app['course']) ?>, Year
                                                <?= htmlspecialchars($app['year_of_study']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($app['opportunity_title']) ?></td>
                                        <td><?= date('M j, Y', strtotime($app['submitted_at'])) ?></td>
                                        <td>
                                            <span class="badge status-badge status-<?= strtolower($app['status']) ?>">
                                                <?= htmlspecialchars($app['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary view-details"
                                                data-id="<?= $app['applications_id'] ?>" data-bs-toggle="modal"
                                                data-bs-target="#applicationModal">
                                                <i class="fas fa-eye me-1"></i> View
                                            </button>
                                            <form method="POST" action="CTrack.php" class="d-inline">
                                                <input type="hidden" name="application_id"
                                                    value="<?= $app['applications_id'] ?>">
                                                <input type="hidden" name="status" value="Accepted">
                                                <button type="submit" name="update_status"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-check me-1"></i> Accept
                                                </button>
                                            </form>
                                            <form method="POST" action="CTrack.php" class="d-inline">
                                                <input type="hidden" name="application_id"
                                                    value="<?= $app['applications_id'] ?>">
                                                <input type="hidden" name="status" value="Rejected">
                                                <button type="submit" name="update_status"
                                                    class="btn btn-sm btn-outline-danger">
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
                        <h5 class="text-muted">No applications received yet</h5>
                        <p class="text-muted">Applications from students will appear here when they apply to your
                            opportunities</p>
                        <a href="COpportunities.php" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-1"></i> Post New Opportunity
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Application Details Modal -->
    <div class="modal fade" id="applicationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Application Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="applicationDetails">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
<<<<<<< HEAD
            <a href="../Help Center.php" class="text-white fw-bold">Help Center</a>
            <a href="../Company/Terms of service.php" class="text-white fw-bold">Terms of service</a>
            <a href="../Company/Contact Support.php" class="text-white fw-bold">Contact Support</a>
=======
            <a href="../../help-center.php" class="text-white fw-bold">Help Center</a>
            <a href="../../terms.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../../contact.php" class="text-white fw-bold">Contact Support: attachme@admin</a>
>>>>>>> 62fb1e5bca71397aa7565d25f7a09ece2b361669
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../Javascript/CTrack.js?v=<?= time() ?>"></script>
</body>

<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> 62fb1e5bca71397aa7565d25f7a09ece2b361669
