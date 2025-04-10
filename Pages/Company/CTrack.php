 

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

<<<<<<< HEAD
        $message = urlencode("Application status updated to {$new_status} successfully!");
        header("Location: CTrack.php?message=$message");
        exit();
    } catch (Exception $e) {
        $error = urlencode("Error updating application status: " . $e->getMessage());
        header("Location: CTrack.php?message=$error&isError=true");
=======
        header("Location: CTrack.php");
>>>>>>> a7fdc1617024d3b49d78499c395d2065200bfe22
        exit();
    }

<<<<<<< HEAD
// Display status message if present in URL
$url_message = '';
$url_message_type = '';
if (isset($_GET['message'])) {
    $url_message = htmlspecialchars(urldecode($_GET['message']));
    $url_message_type = isset($_GET['isError']) ? 'danger' : 'success';
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
=======
    // Build base query
    $sql = "SELECT a.*, s.full_name, o.title 
            FROM applications a
            JOIN students s ON a.student_id = s.student_id
            JOIN opportunities o ON a.opportunities_id = o.opportunities_id
            WHERE o.company_id = ?";
>>>>>>> a7fdc1617024d3b49d78499c395d2065200bfe22

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
<<<<<<< HEAD
                                        <td class="py-4 space-x-2">
                                            <button class="btn btn-sm btn-outline-primary view-details transition-all hover:scale-105"
                                                data-id="<?= $app['applications_id'] ?>" data-bs-toggle="modal"
                                                data-bs-target="#applicationModal">
                                                <i class="fas fa-eye me-1"></i> View
                                            </button>
                                            <!-- <form method="POST" action="CTrack.php" class="inline-flex">
                                                <input type="hidden" name="application_id" value="<?= $app['applications_id'] ?>">
=======
                                        <td class="action-buttons">
                                            <form method="POST" action="CTrack.php" class="d-inline update-status-form">
                                                <input type="hidden" name="application_id"
                                                    value="<?= $app['applications_id'] ?>">
>>>>>>> a7fdc1617024d3b49d78499c395d2065200bfe22
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

<<<<<<< HEAD
    <!-- New Document Viewer Modal -->
    <div class="modal fade" id="applicationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Application Documents</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="applicationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="cover-letter-tab" data-bs-toggle="tab" data-bs-target="#cover-letter" type="button" role="tab">Cover Letter</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="resume-tab" data-bs-toggle="tab" data-bs-target="#resume" type="button" role="tab">Resume</button>
                        </li>
                    </ul>
                    <div class="tab-content p-3 border border-top-0" id="applicationTabsContent">
                        <div class="tab-pane fade show active" id="cover-letter" role="tabpanel">
                            <div class="document-container p-3 bg-light rounded" id="coverLetterContainer">
                                <p class="text-muted">Loading cover letter...</p>
                            </div>
                            <div class="mt-3 text-end">
                                <button class="btn btn-sm btn-primary" id="downloadCoverLetterBtn">
                                    <i class="fas fa-download me-1"></i> Download
                                </button>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="resume" role="tabpanel">
                            <div class="document-container p-3 bg-light rounded" id="resumeContainer">
                                <p class="text-muted">Loading resume...</p>
                            </div>
                            <div class="mt-3 text-end">
                                <button class="btn btn-sm btn-primary" id="downloadResumeBtn">
                                    <i class="fas fa-download me-1"></i> Download
                                </button>
                            </div>
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
=======
    <footer class="bg-dark text-white text-center py-3">
>>>>>>> a7fdc1617024d3b49d78499c395d2065200bfe22
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../../help-center.php" class="text-white fw-bold">Help Center</a>
            <a href="../../terms.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../../contact.php" class="text-white fw-bold">Contact Support: attachme@admin</a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<<<<<<< HEAD
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
    <script>
        // Initialize PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.worker.min.js';
    document.addEventListener('DOMContentLoaded', function() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        let currentApplicationData = null;

        // New document viewer functionality with PDF support
        async function displayDocument(containerId, content, isPdf = false) {
            const container = document.getElementById(containerId);
            container.innerHTML = ''; // Clear previous content
            
            if (!content) {
                container.innerHTML = '<p class="text-danger">Document not available</p>';
                return;
            }
            
            try {
                if (isPdf) {
                    // Handle PDF content
                    const pdfData = atob(content);
                    const pdfDoc = await pdfjsLib.getDocument({data: pdfData}).promise;
                    
                    // Get first page
                    const page = await pdfDoc.getPage(1);
                    const viewport = page.getViewport({scale: 1.0});
                    
                    // Prepare canvas
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    container.appendChild(canvas);
                    
                    // Render PDF page
                    await page.render({
                        canvasContext: context,
                        viewport: viewport
                    }).promise;
                } else {
                    // Handle plain text content
                    if (/^[A-Za-z0-9+/]+={0,2}$/.test(content)) {
                        content = atob(content);
                    }
                    container.innerHTML = `<pre class="document-content">${content}</pre>`;
                }
            } catch (e) {
                console.error('Error displaying document:', e);
                container.innerHTML = `<p class="text-danger">Error displaying document: ${e.message}</p>`;
            }
        }

        function downloadDocument(content, filename, isPdf = false) {
            if (!content) {
                alert('Document not available for download');
                return;
            }
            
            try {
                // Handle base64 content
                if (/^[A-Za-z0-9+/]+={0,2}$/.test(content)) {
                    content = atob(content);
                }
                
                const blob = new Blob([content], { type: isPdf ? 'application/pdf' : 'text/plain' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = isPdf ? filename.replace('.txt', '.pdf') : filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Download error:', error);
                alert('Failed to prepare download. Please try again.');
            }
        }

        // Event listeners for the new modal
        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function() {
                const appId = this.getAttribute('data-id');
                loadingOverlay.style.display = 'flex';
                
                fetch(`../../api/get_application_documents.php?id=${appId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        
                        currentApplicationData = data;

                        // Update student info
                        document.getElementById('modalStudentName').textContent = data.student_name || 'N/A';
                        document.getElementById('modalStudentInfo').textContent = 
                            `${data.student_course || 'N/A'}, Year ${data.student_year || 'N/A'} | ${data.student_email || 'N/A'}`;
                        document.getElementById('modalOpportunity').textContent = 
                            `Applied for: ${data.opportunity_title || 'N/A'}`;
                        
                        // Display documents in the new tabbed interface
                        displayDocument('coverLetterContainer', data.cover_letter);
                        displayDocument('resumeContainer', data.resume);
                        
                        // Set up download buttons
                        document.getElementById('downloadCoverLetterBtn').onclick = () => {
                            downloadDocument(data.cover_letter, 
                                `Cover_Letter_${data.student_name.replace(/\s+/g, '_')}.txt`,
                                data.is_pdf_cover_letter);
                        };
                        
                        document.getElementById('downloadResumeBtn').onclick = () => {
                            downloadDocument(data.resume, 
                                `Resume_${data.student_name.replace(/\s+/g, '_')}.txt`,
                                data.is_pdf_resume);
                        };
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('coverLetterContainer').innerHTML = 
                            `<p class="text-danger">${error.message}</p>`;
                        document.getElementById('resumeContainer').innerHTML = 
                            `<p class="text-danger">${error.message}</p>`;
                    })
                    .finally(() => {
                        loadingOverlay.style.display = 'none';
                    });
            });
        });
    });
    </script>
=======
    <script src="../../Javascript/CTrack.js"></script>
>>>>>>> a7fdc1617024d3b49d78499c395d2065200bfe22
</body>

</html>
 