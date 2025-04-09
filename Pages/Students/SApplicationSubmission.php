<?php
session_start();
require "../../db.php";

// Check if user is logged in as student
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../../SignUps/Slogin.php");
    exit();
}

$student_id = $_SESSION["user_id"];
$opportunity_id = $_GET["opportunities_id"] ?? null;

// Get opportunity details if ID is provided
$opportunity = null;
if ($opportunity_id) {
    try {
        $stmt = $conn->prepare("SELECT title, company_name FROM opportunities WHERE opportunities_id = ?");
        $stmt->execute([$opportunity_id]);
        $opportunity = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error fetching opportunity details: " . $e->getMessage();
    }
}

// Fetch student's existing applications
try {
    $stmt = $conn->prepare("
        SELECT a.*, o.title, o.company_name 
        FROM applications a
        JOIN opportunities o ON a.opportunities_id = o.opportunities_id
        WHERE a.student_id = ?
        ORDER BY a.submitted_at DESC
    ");
    $stmt->execute([$student_id]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching applications: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $application_details = $_POST["application_details"] ?? '';
    $opportunity_id = $_POST["opportunities_id"] ?? null;

    try {
        $stmt = $conn->prepare("
            INSERT INTO applications 
            (student_id, opportunities_id, cover_letter,status, submitted_at) 
            VALUES (?, ?, ?, 'Pending', NOW())
        ");
        $stmt->execute([$student_id, $opportunity_id, $application_details]);

        $_SESSION["success_message"] = "Application submitted successfully!";
        header("Location: SApplicationSubmission.php?success=1");
        exit();
    } catch (PDOException $e) {
        $error = "Error submitting application: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submission - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../CSS/styles.css">
    <script src="../../Javasript/SApplicationSubmission.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME - Student Portal</h2>
            <ul class="navbar-nav d-flex flex-row gap-3">
                <li class="nav-item"><a href="../Students/SDashboard.php" class="nav-link text-white  fs-5 active">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="../Students/SAbout.php" class="nav-link text-white  fs-5 active">📖 About Us</a></li>

                <li class="nav-item"><a href="../Students/SBrowse.php" class="nav-link text-white  fs-5">🔍 Browse Opportunities</a></li>
                <li class="nav-item"><a href="../Students/SApplicationSubmission.php" class="nav-link text-white  fs-5">📄 My Applications</a></li>
                <li class="nav-item"><a href="../Students/SNotifications.php" class="nav-link text-white  fs-5">💬 Messages</a></li>
                <li class="nav-item"><a href="../Students/SProfile.php" class="nav-link text-white  fs-5">👤 Profile</a></li>
            </ul>
        </div>
    </nav>
    
    
    <div class="container mt-4">
        <?php if (isset($_GET["success"])): ?>
            <div class="alert alert-success">
                <?= $_SESSION["success_message"] ?? "Application submitted successfully!" ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <!-- Application Form Section -->
        <?php if ($opportunity_id): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h2>Submit Your Application</h2>
                </div>
                <div class="card-body">
                    <?php if ($opportunity): ?>
                        <div class="alert alert-info mb-4">
                            <h4>Applying for: <?= htmlspecialchars($opportunity["title"]) ?></h4>
                            <p>Company: <?= htmlspecialchars($opportunity["company_name"]) ?></p>
                        </div>
                    <?php endif; ?>

                    <form id="applicationForm" method="POST" action="">
                        <input type="hidden" name="opportunities_id" value="<?= htmlspecialchars($opportunity_id) ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Application Details</label>
                            <textarea class="form-control" name="application_details" required 
                                placeholder="Explain why you're a good fit for this opportunity..." rows="5"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                        <a href="SBrowse.php" class="btn btn-secondary">Back to Opportunities</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Existing Applications Section -->
        <div class="card">
            <div class="card-header">
                <h2>My Applications</h2>
            </div>
            <div class="card-body">
                <?php if (empty($applications)): ?>
                    <div class="alert alert-info">
                        You haven't submitted any applications yet.
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($applications as $app): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5><?= htmlspecialchars($app['title']) ?></h5>
                                        <p class="text-muted"><?= htmlspecialchars($app['company_name']) ?></p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-<?= 
                                            $app['status'] === 'Pending' ? 'warning' : 
                                            ($app['status'] === 'Accepted' ? 'success' : 'danger') 
                                        ?>">
                                            <?= htmlspecialchars($app['status']) ?>
                                        </span>
                                        <p class="text-muted mt-2">
                                            Applied: <?= date('M j, Y', strtotime($app['submitted_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                                <a href="view_application.php?id=<?= $app['applications_id'] ?>" 
                                   class="btn btn-sm btn-outline-primary mt-2">
                                    View Details
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
