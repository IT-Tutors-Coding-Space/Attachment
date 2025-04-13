<?php
require_once "../../db.php";
session_start();

// Check authentication
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: ../../SignUps/CLogin.php");
    exit();
}

$company_id = $_SESSION["user_id"];

// Handle API request for statistics
if (isset($_GET['action']) && $_GET['action'] === 'getStats') {
    header('Content-Type: application/json');

    try {
        // Get total opportunities count
        $opportunitiesStmt = $conn->prepare("SELECT COUNT(*) as count FROM opportunities WHERE company_id = ?");
        $opportunitiesStmt->execute([$company_id]);
        $totalOpportunities = $opportunitiesStmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Get total applications count
        $applicationsStmt = $conn->prepare("SELECT COUNT(*) as count FROM applications 
                                          JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
                                          WHERE opportunities.company_id = ?");
        $applicationsStmt->execute([$company_id]);
        $totalApplications = $applicationsStmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Get accepted applications count
        $acceptedStmt = $conn->prepare("SELECT COUNT(*) as count FROM applications 
                                       JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
                                       WHERE opportunities.company_id = ? AND applications.status = 'Accepted'");
        $acceptedStmt->execute([$company_id]);
        $acceptedApplications = $acceptedStmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Get rejected applications count
        $rejectedStmt = $conn->prepare("SELECT COUNT(*) as count FROM applications 
                                       JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
                                       WHERE opportunities.company_id = ? AND applications.status = 'Rejected'");
        $rejectedStmt->execute([$company_id]);
        $rejectedApplications = $rejectedStmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Get pending applications count
        $pendingStmt = $conn->prepare("SELECT COUNT(*) as count FROM applications 
                                     JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
                                     WHERE opportunities.company_id = ? AND applications.status = 'Pending'");
        $pendingStmt->execute([$company_id]);
        $pendingApplications = $pendingStmt->fetch(PDO::FETCH_ASSOC)['count'];

        echo json_encode([
            'success' => true,
            'totalOpportunities' => $totalOpportunities,
            'totalApplications' => $totalApplications,
            'acceptedApplications' => $acceptedApplications,
            'rejectedApplications' => $rejectedApplications,
            'pendingApplications' => $pendingApplications
        ]);
        exit();

    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
        exit();
    }
}

// Fetch initial data for page load
try {
    // Fetch company name
    $companyStmt = $conn->prepare("SELECT company_name FROM companies WHERE company_id = ?");
    $companyStmt->execute([$company_id]);
    $company = $companyStmt->fetch(PDO::FETCH_ASSOC);
    $company_name = $company ? htmlspecialchars($company["company_name"]) : "Unknown Company";

    // Fetch initial statistics
    $statsStmt = $conn->prepare("
        SELECT 
            (SELECT COUNT(*) FROM opportunities WHERE company_id = ?) as totalOpportunities,
            (SELECT COUNT(*) FROM applications 
             JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
             WHERE opportunities.company_id = ?) as totalApplications,
            (SELECT COUNT(*) FROM applications 
             JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
             WHERE opportunities.company_id = ? AND applications.status = 'Accepted') as acceptedApplications,
            (SELECT COUNT(*) FROM applications 
             JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
             WHERE opportunities.company_id = ? AND applications.status = 'Rejected') as rejectedApplications,
            (SELECT COUNT(*) FROM applications 
             JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
             WHERE opportunities.company_id = ? AND applications.status = 'Pending') as pendingApplications
    ");
    $statsStmt->execute([$company_id, $company_id, $company_id, $company_id, $company_id]);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

    // Fetch recent applications
    $recentAppsStmt = $conn->prepare("
        SELECT applications.*, students.full_name, opportunities.title 
        FROM applications 
        JOIN students ON applications.student_id = students.student_id
        JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
        WHERE opportunities.company_id = ?
        ORDER BY applications.submitted_at DESC
        LIMIT 5
    ");
    $recentAppsStmt->execute([$company_id]);
    $recentApplications = $recentAppsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../CSS/CTrack.css">
    <style>
        .stat-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 10px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .count-display {
            font-size: 2.2rem;
            font-weight: 600;
        }

        .recent-applications {
            border-radius: 10px;
            overflow: hidden;
        }

        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-white" href="CHome.php">AttachME</a>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="CHome.php" class="nav-link text-white fw-bold fs-5 active">Dashboard</a>
                </li>
                <li class="nav-item"><a href="COpportunities.php"
                        class="nav-link text-white fw-bold fs-5">Opportunities</a></li>
                <li class="nav-item"><a href="CTrack.php" class="nav-link text-white fw-bold fs-5">Applications</a></li>
                <li class="nav-item"><a href="CNotifications.php" class="nav-link text-white fw-bold fs-5">Messages</a>
                </li>
                <li class="nav-item"><a href="CProfile.php" class="nav-link text-white fw-bold fs-5">Profile</a></li>
            </ul>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="fw-bold mb-4">Welcome, <?php echo $company_name; ?></h2>

        <!-- Statistics Cards -->
        <div class="row mb-5 g-4">
            <!-- Total Opportunities -->
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card stat-card bg-primary bg-gradient text-white h-100">
                    <div class="card-body text-center py-4">
                        <h5 class="card-title">Total Opportunities</h5>
                        <h2 class="count-display" id="totalOpportunitiesCount">
                            <?php echo $stats['totalOpportunities'] ?? 0; ?></h2>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 py-3">
                        <small class="opacity-75">Opportunities posted</small>
                    </div>
                </div>
            </div>

            <!-- Total Applications -->
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card stat-card bg-info bg-gradient text-white h-100">
                    <div class="card-body text-center py-4">
                        <h5 class="card-title">Total Applications</h5>
                        <h2 class="count-display" id="totalApplicationsCount">
                            <?php echo $stats['totalApplications'] ?? 0; ?></h2>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 py-3">
                        <small class="opacity-75">Applications received</small>
                    </div>
                </div>
            </div>

            <!-- Accepted Applications -->
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card stat-card bg-success bg-gradient text-white h-100">
                    <div class="card-body text-center py-4">
                        <h5 class="card-title">Accepted</h5>
                        <h2 class="count-display" id="acceptedApplicationsCount">
                            <?php echo $stats['acceptedApplications'] ?? 0; ?></h2>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 py-3">
                        <small class="opacity-75">Applications accepted</small>
                    </div>
                </div>
            </div>

            <!-- Rejected Applications -->
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card stat-card bg-danger bg-gradient text-white h-100">
                    <div class="card-body text-center py-4">
                        <h5 class="card-title">Rejected</h5>
                        <h2 class="count-display" id="rejectedApplicationsCount">
                            <?php echo $stats['rejectedApplications'] ?? 0; ?></h2>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 py-3">
                        <small class="opacity-75">Applications rejected</small>
                    </div>
                </div>
            </div>

            <!-- Pending Applications -->
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card stat-card bg-warning bg-gradient text-dark h-100">
                    <div class="card-body text-center py-4">
                        <h5 class="card-title">Pending</h5>
                        <h2 class="count-display" id="pendingApplicationsCount">
                            <?php echo $stats['pendingApplications'] ?? 0; ?></h2>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 py-3">
                        <small class="opacity-75">Applications pending</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Applications Section -->
        <div class="card shadow-sm recent-applications">
            <div class="card-header bg-white">
                <h5 class="mb-0">Recent Applications</h5>
            </div>
            <div class="card-body p-0">
                <?php if (count($recentApplications) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Opportunity</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentApplications as $app): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($app['full_name']) ?></td>
                                        <td><?= htmlspecialchars($app['title']) ?></td>
                                        <td><?= date('M j, Y', strtotime($app['submitted_at'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?=
                                                $app['status'] === 'Accepted' ? 'success' :
                                                ($app['status'] === 'Rejected' ? 'danger' : 'warning') ?>">
                                                <?= htmlspecialchars($app['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No recent applications found</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-white text-end py-3">
                <a href="CTrack.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-list me-1"></i> View All Applications
                </a>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../../help-center.php" class="text-white fw-bold">Help Center</a>
            <a href="../../terms.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../../contact.php" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../Javascript/CHome.js"></script>
</body>

</html>