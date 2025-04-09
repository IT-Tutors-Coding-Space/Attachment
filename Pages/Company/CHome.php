<?php
require_once "../../db.php";
session_start();

// Handle application status changes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['application_id'])) {
    try {
        $status = ($_POST['action'] === 'accept') ? 'Accepted' : 'Rejected';
        $updateStmt = $conn->prepare("UPDATE applications SET status = ? WHERE application_id = ?");
        $updateStmt->execute([$status, $_POST['application_id']]);
        
        // Refresh page to show updated status
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        $error = "Error updating application: " . $e->getMessage();
    }
}

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: ../../SignUps/CLogin.php");
    exit();
}

$company_id = $_SESSION["user_id"];

try {
    // Fetch company name
    $companyStmt = $conn->prepare("SELECT company_name FROM companies WHERE company_id = ?");
    $companyStmt->execute([$company_id]);
    $company = $companyStmt->fetch(PDO::FETCH_ASSOC);
    $company_name = $company ? htmlspecialchars($company["company_name"]) : "Unknown Company";

    // Fetch opportunities
    $opportunitiesStmt = $conn->prepare("SELECT * FROM opportunities WHERE company_id = ?");
    $opportunitiesStmt->execute([$company_id]);
    $opportunities = $opportunitiesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch applications (UNCOMMENTED)
    $applicationsStmt = $conn->prepare("SELECT applications.*, students.full_name, opportunities.title 
                                        FROM applications 
                                        JOIN students ON applications.student_id = students.student_id
                                        JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
                                        WHERE opportunities.company_id = ?");
    $applicationsStmt->execute([$company_id]);
    $applications = $applicationsStmt->fetchAll(PDO::FETCH_ASSOC);
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../CSS/CHome.css">
    <style>
        /* Additional inline styles if needed */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
        }
    </style>
</head>

<body>
    <!-- Top Navigation Bar (Static) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3" style="position: static;">
        <div class="container-fluid d-flex justify-content-between">
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="../Company/CHome.php" class="nav-link text-white fw-bold fs-5 active">
                        Dashboard</a></li>
                <li class="nav-item"><a href="../Company/COpportunities.php" class="nav-link text-white fw-bold fs-5">
                        Opportunities</a></li>
                <li class="nav-item"><a href="../Company/CTrack.php" class="nav-link text-white fw-bold fs-5">
                        Applications</a></li>
                <li class="nav-item"><a href="../Company/CNotifications.php" class="nav-link text-white fw-bold fs-5">
                        Messages</a></li>
                <li class="nav-item"><a href="../Company/CProfile.php" class="nav-link text-white fw-bold fs-5">
                        Profile</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content container p-5">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <div>
                <h1 class="text-primary fw-bold fs-4">Welcome, <?php echo $company_name; ?></h1>
                <p class="text-muted mb-0">Monitor your posted opportunities, applications, and engagement insights.</p>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card bg-primary">
                    <h5 class="fw-bold">Total Opportunities</h5>
                    <h2 class="fw-bold"><?php echo count($opportunities); ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-success">
                    <h5 class="fw-bold">Total Applications</h5>
                    <h2 class="fw-bold"><?php echo count($applications); ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-warning">
                    <h5 class="fw-bold">Pending Applications</h5>
                    <h2 class="fw-bold">
                        <?php echo count(array_filter($applications, fn($app) => $app["status"] === "Pending")); ?>
                    </h2>
                </div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
            <h5 class="fw-bold fs-5 mb-3">Recent Applications</h5>
            <div class="table-responsive">
                <table class="application-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Opportunity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $application): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($application["full_name"]); ?></td>
                                <td><?php echo htmlspecialchars($application["title"]); ?></td>
                                <td>
                                    <span class="badge bg-<?php
                                    echo ($application["status"] === "Accepted") ? "success" :
                                        (($application["status"] === "Pending") ? "warning" : "danger"); ?>">
                                        <?php echo htmlspecialchars($application["status"]); ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="post" style="display:inline">
                                        <input type="hidden" name="applications_id" value="<?php echo $application['applications_id']; ?>">
                                        <button type="submit" name="action" value="accept" class="btn btn-sm btn-outline-success">Accept</button>
                                    </form>
                                    <form method="post" style="display:inline">
                                        <input type="hidden" name="applications_id" value="<?php echo $application['applications_id']; ?>">
                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer (Static) -->
    <footer class="bg-dark text-white text-center py-3" style="position: static;">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="footer-links">
            <a href="../../help-center.php" class="text-white">Help Center</a>
            <a href="../../terms.php" class="text-white">Terms of Service</a>
            <a href="../../contact.php" class="text-white">Contact Support:</a>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javascript/CHome.js"></script>
</body>

</html>
