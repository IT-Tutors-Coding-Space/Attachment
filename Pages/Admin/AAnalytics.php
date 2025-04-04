<?php
require_once "../../db.php";
session_start();

// Check if the user is logged in
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: Alogin.php"); // Redirect to login page if not authenticated
//     exit();
// }

// Fetch analytics data from the database
$totalApplications = $conn->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$acceptedApplications = $conn->query("SELECT COUNT(*) FROM applications WHERE status = 'Accepted'")->fetchColumn();
$rejectedApplications = $conn->query("SELECT COUNT(*) FROM applications WHERE status = 'Rejected'")->fetchColumn();
$activeCompanies = $conn->query("SELECT COUNT(*) FROM companies WHERE status = 'active'")->fetchColumn();

// Get applications per company data
$applicationsPerCompany = $conn->query("
    SELECT c.company_name, COUNT(a.applications_id) AS application_count 
    FROM companies c
    LEFT JOIN opportunities o ON c.company_id = o.company_id
    LEFT JOIN applications a ON o.opportunities_id = a.opportunities_id
    GROUP BY c.company_id
    ORDER BY application_count DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Get application status distribution
$statusDistribution = $conn->query("
    SELECT status, COUNT(*) AS count 
    FROM applications 
    GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);

// Get applications over time (last 30 days)
$applicationsOverTime = $conn->query("
    SELECT DATE(submitted_at) AS date, COUNT(*) AS count
    FROM applications
    WHERE submitted_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(submitted_at)
    ORDER BY date
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttachME Admin - Analytics</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/admin-styles.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME</h2>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="../Admin/AHome.php" class="nav-link text-white fw-bold fs-5">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="../Admin/AUsers.php" class="nav-link text-white fw-bold fs-5">👤 Users</a></li>
                <li class="nav-item"><a href="../Admin/ACompanies.php" class="nav-link text-white fw-bold fs-5">🏢 Companies</a></li>
                <li class="nav-item"><a href="../Admin/AOpportunities.php" class="nav-link text-white fw-bold fs-5">📢 Opportunities</a></li>
                <li class="nav-item"><a href="../Admin/AApplications.php" class="nav-link text-white fw-bold fs-5 active">📄 Applications</a></li>
                <li class="nav-item"><a href="../Admin/AAnalytics.php" class="nav-link text-white fw-bold fs-5">📊 Analytics</a></li>
                <li class="nav-item"><a href="../Admin/ASettings.php" class="nav-link text-white fw-bold fs-5">⚙️ Settings</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h1 class="text-3xl fw-bold">Analytics Dashboard</h1>
            <p class="text-muted">Visual representation of system performance, application trends, and company engagement.</p>
        </header>

        <!-- Analytics Overview -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 bg-primary text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Total Applications</h5>
                    <h2 id="totalApplications" class="fw-bold fs-3">0</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 bg-success text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Accepted Applications</h5>
                    <h2 id="acceptedApplications" class="fw-bold fs-3">0</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 bg-danger text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Rejected Applications</h5>
                    <h2 id="rejectedApplications" class="fw-bold fs-3">0</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 bg-warning text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Active Companies</h5>
                    <h2 id="activeCompanies" class="fw-bold fs-3">0</h2>
                </div>
            </div>
        </div>

        <!-- Analytics Charts -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                    <h5 class="fw-bold fs-5 mb-3">Applications per Company</h5>
                    <canvas id="applicationsChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                    <h5 class="fw-bold fs-5 mb-3">Accepted vs. Rejected Applications</h5>
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="#help" class="text-white fw-bold">Help Center</a>
            <a href="#terms" class="text-white fw-bold">Terms of Service</a>
            <a href="#contact" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Pass PHP data to JavaScript -->
    <script>
        const analyticsData = {
            totalApplications: <?php echo $totalApplications; ?>,
            acceptedApplications: <?php echo $acceptedApplications; ?>,
            rejectedApplications: <?php echo $rejectedApplications; ?>,
            activeCompanies: <?php echo $activeCompanies; ?>,
            applicationsPerCompany: <?php echo json_encode($applicationsPerCompany); ?>,
            statusDistribution: <?php echo json_encode($statusDistribution); ?>
        };
    </script>
    
    <!-- Custom JavaScript -->
    <script src="../../Javasript/AAnalytics.js"></script>
</body>
</html>
