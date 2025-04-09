<?php
require_once "../../db.php";
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student"){
    header("Location: ../../SignUps/Slogin.php");
    exit();
}
$student_id = $_SESSION["user_id"];
require "../../Components/StudentNav.php";
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
    
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <?php if (isset($_SESSION['application_success'])): ?>
            <div class="alert alert-success mb-4">
                <?= $_SESSION['application_success'] ?>
                <?php unset($_SESSION['application_success']); ?>
            </div>
        <?php endif; ?>
<br><br><br>
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
                            <?php
                            // Check if student has already applied
                            $applied_stmt = $conn->prepare("
                                SELECT 1 FROM applications 
                                WHERE student_id = ? AND opportunities_id = ?
                            ");
                            $applied_stmt->execute([$student_id, $opportunity["opportunities_id"]]);
                            $has_applied = $applied_stmt->fetch();
                            
                            if ($has_applied): ?>
                                <span class="badge bg-success p-3">Applied ✓</span>
                            <?php else: ?>
                                <button class="btn btn-primary btn-lg apply-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#applyModal"
                                        data-opportunity-id="<?php echo htmlspecialchars($opportunity["opportunities_id"]); ?>">
                                    Apply Now
                                </button>
                            <?php endif; ?>
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
                            <form id="applicationForm" action="../../api/submit-application.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                            <input type="hidden" name="opportunities_id" id="modalOpportunityId" value="">
                            
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

    <?php require "../../Components/StudentFooter.php"; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javasript/SBrowse.js?v=<?= time() ?>"></script>
    <script>
    // Enhanced live search functionality
    let searchTimeout;
    const searchInput = document.getElementById('searchOpportunities');
    const opportunityCards = document.querySelectorAll('#opportunitiesList .col-md-12');
    const noResultsMsg = document.createElement('div');
    noResultsMsg.className = 'col-12 text-center py-4';
    noResultsMsg.innerHTML = '<h4 class="text-muted">No matching opportunities found</h4>';
    noResultsMsg.style.display = 'none';
    document.getElementById('opportunitiesList').appendChild(noResultsMsg);

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = this.value.trim().toLowerCase();
            let hasMatches = false;
            
            opportunityCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const company = card.querySelector('h5').textContent.toLowerCase();
                const location = card.querySelector('p:nth-of-type(4)').textContent.toLowerCase();
                
                const matches = title.includes(searchTerm) || 
                              company.includes(searchTerm) || 
                              location.includes(searchTerm);
                
                card.style.display = matches ? 'block' : 'none';
                if (matches) hasMatches = true;
            });

            noResultsMsg.style.display = searchTerm && !hasMatches ? 'block' : 'none';
        }, 300); // 300ms delay
    });

    // Set opportunity ID when apply button clicked
    document.querySelectorAll('.apply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modalOpportunityId').value = 
                this.getAttribute('data-opportunity-id');
        });
    });
    </script>
</body>
</html>
 