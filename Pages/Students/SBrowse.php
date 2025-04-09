<?php
require_once "../../db.php";
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student"){
    header("Location: ../../SignUps/Slogin.php");
    exit();
}
$student_id = $_SESSION["user_id"];
try {
    $stmt = $conn->prepare("SELECT * FROM opportunities");
    $stmt->execute();
    $opportunities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Opportunities - AttachME</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/student-styles.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME - Student Portal</h2>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="../Students/SDashboard.php" class="nav-link text-white fw-bold fs-5 active">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="../Students/SAbout.php" class="nav-link text-white fw-bold fs-5 active">📖 About Us</a></li>

                <li class="nav-item"><a href="../Students/SBrowse.php" class="nav-link text-white fw-bold fs-5">🔍 Browse Opportunities</a></li>
                <li class="nav-item"><a href="../Students/SApplicationSubmission.php" class="nav-link text-white fw-bold fs-5">📄 My Applications</a></li>
                <li class="nav-item"><a href="../Students/SNotifications.php" class="nav-link text-white fw-bold fs-5">💬 Messages</a></li>
                <li class="nav-item"><a href="../Students/SProfile.php" class="nav-link text-white fw-bold fs-5">👤 Profile</a></li>
            </ul>
        </div>
    </nav><br><br><br>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h1 class="text-3xl fw-bold">Explore Attachment Opportunities</h1>
            <input type="text" class="form-control w-50" id="searchOpportunities" placeholder="🔍 Search by title, company, or location...">
        </header>

        <!-- Opportunities List -->
        <div class="row g-4" id="opportunitiesList">
            <?php foreach ($opportunities as $opportunity): ?>
            <div class="col-md-12 mb-4">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="fw-bold"><?php echo htmlspecialchars($opportunity["title"]); ?></h3>
                            <h5 class="text-muted"><?php echo htmlspecialchars($opportunity["company_name"]); ?></h5>
                            <p class="mt-3"><strong>Description:</strong><br><?php echo htmlspecialchars($opportunity["description"]); ?></p>
                            <p><strong>Requirements:</strong><br><?php echo htmlspecialchars($opportunity["requirements"]); ?></p>
                            <p><strong>Duration:</strong> <?php echo htmlspecialchars($opportunity["duration"]); ?> months</p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($opportunity["location"]); ?></p>
                            <p><strong>Deadline:</strong> <?php echo htmlspecialchars($opportunity["application_deadline"]); ?></p>
                        </div>
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <button class="btn btn-primary btn-lg apply-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#applyModal"
                                    data-opportunity-id="<?php echo htmlspecialchars($opportunity["opportunities_id"]); ?>">
                                Apply Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Application Modal -->
        <div class="modal fade" id="applyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Apply for Opportunity</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="applicationForm" action="/Attachment/api/application-submit.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Upload Cover Letter (PDF only)</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="form-control" name="cover_letter" accept=".pdf" required>
                                    <small class="text-muted">Max size: 5MB</small>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Upload Resume (PDF only)</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="form-control" name="resume" accept=".pdf" required>
                                    <small class="text-muted">Max size: 5MB</small>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Submit Application</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
        // Set opportunity ID when apply button clicked
        document.querySelectorAll('.apply-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('modalOpportunityId').value = 
                    this.getAttribute('data-opportunity-id');
            });
        });
        </script>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../Help Center.php" class="text-white fw-bold">Help Center</a>
            <a href="../Students/Terms of service.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../Students/Contact Support.php" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javasript/SBrowse.js?v=<?= time() ?>"></script>
</body>
</html>
 