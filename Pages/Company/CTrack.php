 

<?php
require_once "../../db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: ../../SignUps/CLogin.php");
    exit();
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
        $application_id = filter_input(INPUT_POST, 'application_id', FILTER_VALIDATE_INT);
        $new_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!$application_id || !$new_status) {
            throw new Exception("Invalid input data");
        }

        // Update query with proper company ownership check
        $updateStmt = $conn->prepare("
            UPDATE applications a
            JOIN opportunities o ON a.opportunities_id = o.opportunities_id
            SET a.status = ? 
            WHERE a.applications_id = ? 
            AND o.company_id = ?
        ");
        $updateStmt->execute([$new_status, $application_id, $company_id]);

        if ($updateStmt->rowCount() > 0) {
            $_SESSION['message'] = "Status updated successfully!";
            $_SESSION['message_type'] = "success";
        } 
        else {
            throw new Exception("No application found or you don't have permission to update it");
        }

        header("Location: CTrack.php");
        exit();
    }

    // Build base query
    $sql = "SELECT a.*, s.full_name, o.title 
            FROM applications a
            JOIN students s ON a.student_id = s.student_id
            JOIN opportunities o ON a.opportunities_id = o.opportunities_id
            WHERE o.company_id = ?";

    // Add filter condition
    $params = [$company_id];
    if ($filter !== 'all') {
        $sql .= " AND a.status = ?";
        $params[] = $filter;
    }

    // Add sorting
    $sql .= " ORDER BY a.submitted_at DESC";

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
    <!-- <title>Application Tracking - AttachME</title> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../CSS/CTrack.css">
    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1 0 auto;
        }

        footer {
            flex-shrink: 0;
        }

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
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid">
            <!-- <a class="navbar-brand fw-bold text-white" href="CHome.php">AttachME - Opportunities</a> -->
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

    <div class="main-content container p-5">
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
                                    <!-- <th>Status</th> -->
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
                                            <form method="POST" action="CTrack.php" class="d-inline update-status-form">
                                                <input type="hidden" name="application_id"
                                                    value="<?= $app['applications_id'] ?>">
                                                <input type="hidden" name="status" value="Accepted">
                                                <button type="submit" name="update_status"
                                                    class="btn btn-sm btn-outline-success <?= $app['status'] === 'Accepted' ? 'disabled' : '' ?>">
                                                    <i class="fas fa-check me-1"></i> Accept
                                                </button>
                                            </form>
                                            <form method="POST" action="CTrack.php" class="d-inline update-status-form">
                                                <input type="hidden" name="application_id"
                                                    value="<?= $app['applications_id'] ?>">
                                                <input type="hidden" name="status" value="Rejected">
                                                <button type="submit" name="update_status"
                                                    class="btn btn-sm btn-outline-danger <?= $app['status'] === 'Rejected' ? 'disabled' : '' ?>">
                                                    <i class="fas fa-times me-1"></i> Reject
                                                </button>
                                            </form>
                                        </td> -->
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

    <footer class="bg-dark text-white text-center py-3">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../../help-center.php" class="text-white fw-bold">Help Center</a>
            <a href="../../terms.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../../contact.php" class="text-white fw-bold">Contact Support: attachme@admin</a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../Javascript/CTrack.js"></script>
</body>

</html>
 j