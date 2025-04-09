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
            a.applications_id, a.status, a.submitted_at, a.cover_letter, a.resume,
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
    <title>Application Tracking - AttachME</title>
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
                                    <tr class="application-card hover:bg-gray-50 transition-colors duration-200">
                                        <td class="py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-blue-600"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="font-medium text-gray-900"><?= htmlspecialchars($app['full_name']) ?></div>
                                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($app['email']) ?></div>
                                                    <div class="text-xs text-gray-400 mt-1">
                                                        <?= htmlspecialchars($app['course']) ?>, Year <?= htmlspecialchars($app['year_of_study']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <div class="text-gray-900 font-medium"><?= htmlspecialchars($app['opportunity_title']) ?></div>
                                            <div class="text-sm text-gray-500">Submitted: <?= date('M j, Y', strtotime($app['submitted_at'])) ?></div>
                                        </td>
                                        <td class="py-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                <?= $app['status'] === 'Accepted' ? 'bg-green-100 text-green-800' : 
                                                   ($app['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                                <?= htmlspecialchars($app['status']) ?>
                                            </span>
                                        </td>
                                        <td class="py-4 space-x-2">
                                            <button class="btn btn-sm btn-outline-primary view-details transition-all hover:scale-105"
                                                data-id="<?= $app['applications_id'] ?>" data-bs-toggle="modal"
                                                data-bs-target="#applicationModal">
                                                <i class="fas fa-eye me-1"></i> View
                                            </button>
                                            <form method="POST" action="CTrack.php" class="inline-flex">
                                                <input type="hidden" name="application_id" value="<?= $app['applications_id'] ?>">
                                                <input type="hidden" name="status" value="Accepted">
                                                <button type="submit" name="update_status"
                                                    class="btn btn-sm btn-outline-success transition-all hover:scale-105">
                                                    <i class="fas fa-check me-1"></i> Accept
                                                </button>
                                            </form>
                                            <form method="POST" action="CTrack.php" class="inline-flex">
                                                <input type="hidden" name="application_id" value="<?= $app['applications_id'] ?>">
                                                <input type="hidden" name="status" value="Rejected">
                                                <button type="submit" name="update_status"
                                                    class="btn btn-sm btn-outline-danger transition-all hover:scale-105">
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

    <!-- Enhanced Application Details Modal -->
    <div class="modal fade" id="applicationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-blue-600 text-white">
                    <h5 class="modal-title font-bold">Application Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                        <!-- Cover Letter Section -->
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                                <i class="fas fa-envelope-open-text text-blue-500 mr-2"></i>Cover Letter
                            </h3>
                            <div class="prose max-w-none" id="modalCoverLetter">
                                <p class="text-gray-600">Loading cover letter...</p>
                            </div>
                        </div>
                        
                        <!-- Resume Section -->
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                                <i class="fas fa-file-alt text-blue-500 mr-2"></i>Resume
                            </h3>
                            <div class="prose max-w-none" id="modalResume">
                                <p class="text-gray-600">Loading resume...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Student Info Section -->
                    <div class="bg-gray-100 p-4 border-t">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800" id="modalStudentName">Loading...</h4>
                                <p class="text-gray-600" id="modalStudentInfo"></p>
                                <p class="text-sm text-gray-500" id="modalOpportunity"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 px-5 py-4 border-t">
                    <div class="flex justify-between w-full">
                        <div class="space-x-2">
                            <form method="POST" action="CTrack.php" class="inline-flex">
                                <input type="hidden" name="application_id" id="modalAppId">
                                <input type="hidden" name="status" value="Accepted">
                                <button type="submit" name="update_status"
                                    class="btn btn-success transition-all hover:scale-105">
                                    <i class="fas fa-check me-1"></i> Accept Application
                                </button>
                            </form>
                            <form method="POST" action="CTrack.php" class="inline-flex">
                                <input type="hidden" name="application_id" id="modalAppId2">
                                <input type="hidden" name="status" value="Rejected">
                                <button type="submit" name="update_status"
                                    class="btn btn-danger transition-all hover:scale-105">
                                    <i class="fas fa-times me-1"></i> Reject Application
                                </button>
                            </form>
                        </div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../../help-center.php" class="text-white fw-bold">Help Center</a>
            <a href="../../terms.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../../contact.php" class="text-white fw-bold">Contact Support:</a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        let currentApplicationData = null;

        // Debug function to log data
        function debugLog(message, data) {
            console.log(message, data);
            // Uncomment below to also log to server for debugging
            // fetch('../../api/debug_log.php', {
            //     method: 'POST',
            //     headers: {'Content-Type': 'application/json'},
            //     body: JSON.stringify({message, data})
            // });
        }

        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function() {
                const appId = this.getAttribute('data-id');
                debugLog("Fetching application ID:", appId);
                
                fetch(`../../api/get_application.php?id=${appId}`)
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        debugLog("Application data received:", data);
                        if (!data) throw new Error("No data received");
                        
                        currentApplicationData = data;

                        // Update student info
                        document.getElementById('modalStudentName').textContent = data.full_name || 'N/A';
                        document.getElementById('modalStudentInfo').textContent = 
                            `${data.course || 'N/A'}, Year ${data.year_of_study || 'N/A'} | ${data.email || 'N/A'}`;
                        document.getElementById('modalOpportunity').textContent = 
                            `Applied for: ${data.opportunity_title || 'N/A'}`;
                        
                        // Update cover letter
                        const coverLetterDiv = document.querySelector('#modalCoverLetter .prose');
                        if (data.cover_letter) {
                            coverLetterDiv.innerHTML = data.cover_letter;
                            document.getElementById('downloadCoverLetter').style.display = 'inline-block';
                        } else {
                            coverLetterDiv.innerHTML = '<p class="text-gray-400">No cover letter provided</p>';
                            document.getElementById('downloadCoverLetter').style.display = 'none';
                        }

                        // Update resume
                        const resumeDiv = document.querySelector('#modalResume .prose');
                        if (data.resume) {
                            resumeDiv.innerHTML = data.resume;
                            document.getElementById('downloadResume').style.display = 'inline-block';
                        } else {
                            resumeDiv.innerHTML = '<p class="text-gray-400">No resume provided</p>';
                            document.getElementById('downloadResume').style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        debugLog("Fetch error:", error.message);
                        
                        const errorMsg = `Error: ${error.message}`;
                        document.querySelector('#modalCoverLetter .prose').innerHTML = 
                            `<p class="text-danger">${errorMsg}</p>`;
                        document.querySelector('#modalResume .prose').innerHTML = 
                            `<p class="text-danger">${errorMsg}</p>`;
                    });
            });
        });

        // Download functionality
        function downloadFile(content, filename, type = 'text/plain') {
            if (!content) return;
            
            try {
                const blob = new Blob([content], { type });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Download error:', error);
                alert('Failed to prepare download. Please try again.');
            }
        }

        document.getElementById('downloadCoverLetter')?.addEventListener('click', function() {
            if (currentApplicationData?.cover_letter) {
                const filename = `Cover_Letter_${currentApplicationData.full_name.replace(/\s+/g, '_')}.txt`;
                downloadFile(currentApplicationData.cover_letter, filename);
            }
        });

        document.getElementById('downloadResume')?.addEventListener('click', function() {
            if (currentApplicationData?.resume) {
                const filename = `Resume_${currentApplicationData.full_name.replace(/\s+/g, '_')}.txt`;
                downloadFile(currentApplicationData.resume, filename);
            }
        });
    });
    </script>
</body>

</html>
