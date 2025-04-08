<?php
session_start();
require_once "../../db.php";

// Check if the user is authenticated and is a company
if (!isset($_SESSION["user_id"])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        http_response_code(403); // Forbidden
        echo json_encode(["success" => false, "message" => "Unauthorized access."]);
        exit();
    } else {
        header("Location: /login.php");
        exit();
    }
}

$message = null;
$messageType = null; // 'success' or 'danger'
$showForm = isset($_GET['create']) ? true : false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input fields
    if (empty($_POST["title"]) || empty($_POST["description"]) || empty($_POST["requirements"]) || empty($_POST["available_slots"]) || empty($_POST["application_deadline"])) {
        $message = "All fields are required.";
        $messageType = "danger";
    }

    // Validate application deadline (must be in the future)
    $deadline = $_POST["application_deadline"];
    if (empty($message) && strtotime($deadline) <= time()) {
        $message = "Application deadline must be in the future.";
        $messageType = "danger";
    }

    // Validate available slots (must be a positive integer)
    $slots = $_POST["available_slots"];
    if (empty($message) && (!ctype_digit($slots) || intval($slots) <= 0)) {
        $message = "Available slots must be a positive integer.";
        $messageType = "danger";
    }

    // If no validation errors, proceed with database insertion
    if (empty($message)) {
        $companyId = $_SESSION["user_id"];
        $status = "open";

        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("INSERT INTO opportunities (company_id, title, description, requirements, available_slots, application_deadline, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$companyId, $_POST["title"], $_POST["description"], $_POST["requirements"], $_POST["available_slots"], $_POST["application_deadline"], $status]);

            $conn->commit();

            $message = "Opportunity posted successfully!";
            $messageType = "success";
            $showForm = false; // Hide form after success

            // For AJAX requests, return JSON response
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(["success" => true, "message" => $message]);
                exit();
            }

        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $message = "Error: " . $e->getMessage();
            $messageType = "danger";

            // For AJAX requests, return JSON response
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(["success" => false, "message" => $message]);
                exit();
            }
        }
    } else {
        // For AJAX requests, return JSON response for validation errors
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo json_encode(["success" => false, "message" => $message]);
            exit();
        }
    }
}

// Fetch all opportunities for this company
$opportunities = [];
try {
    $stmt = $conn->prepare("SELECT title, requirements, available_slots, application_deadline, status FROM opportunities WHERE company_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION["user_id"]]);
    $opportunities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error fetching opportunities: " . $e->getMessage();
    $messageType = "danger";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opportunities - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/COpportunities.css">
    <style>
        .opportunity-card {
            transition: all 0.3s ease;
        }

        .opportunity-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>

<body class="bg-gray-100 d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-white" href="CHome.php">🏢 AttachME - Opportunities</a>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="CHome.php" class="nav-link text-white fw-bold fs-5">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="COpportunities.php" class="nav-link text-white fw-bold fs-5 active">📢
                        Opportunities</a></li>
                <li class="nav-item"><a href="CTrack.php" class="nav-link text-white fw-bold fs-5">📄 Applications</a>
                </li>
                <li class="nav-item"><a href="CNotifications.php" class="nav-link text-white fw-bold fs-5">💬
                        Messages</a></li>
                <li class="nav-item"><a href="CProfile.php" class="nav-link text-white fw-bold fs-5">🏢 Profile</a></li>
            </ul>
        </div>
    </nav>

    <div class="container p-5 flex-grow-1">
        <!-- Message display area -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show mb-4"
                role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">Your Opportunities</h2>
            <?php if (!$showForm): ?>
                <a href="COpportunities.php?create=true" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Opportunity
                </a>
            <?php endif; ?>
        </div>

        <!-- Create Opportunity Form (shown only when $showForm is true) -->
        <?php if ($showForm): ?>
            <div class="card border-0 shadow-sm p-4 bg-white rounded-lg mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary">Post a New Opportunity</h5>
                    <button onclick="window.location.href='COpportunities.php'" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="opportunityForm" method="POST" action="COpportunities.php">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required
                            value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="requirements" class="form-label">Requirements</label>
                        <textarea class="form-control" id="requirements" name="requirements" rows="3"
                            required><?php echo isset($_POST['requirements']) ? htmlspecialchars($_POST['requirements']) : ''; ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="available_slots" class="form-label">Available Slots</label>
                            <input type="number" class="form-control" id="available_slots" name="available_slots" required
                                value="<?php echo isset($_POST['available_slots']) ? htmlspecialchars($_POST['available_slots']) : ''; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="application_deadline" class="form-label">Application Deadline</label>
                            <input type="date" class="form-control" id="application_deadline" name="application_deadline"
                                required
                                value="<?php echo isset($_POST['application_deadline']) ? htmlspecialchars($_POST['application_deadline']) : ''; ?>">
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Post Opportunity</button>
                        <button type="button" onclick="window.location.href='COpportunities.php'"
                            class="btn btn-outline-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Opportunities Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <?php if (count($opportunities) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Requirements</th>
                                    <th>Available Slots</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($opportunities as $opportunity): ?>
                                    <tr class="opportunity-card">
                                        <td><?php echo htmlspecialchars($opportunity['title']); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars(substr($opportunity['requirements'], 0, 100) . (strlen($opportunity['requirements']) > 100 ? '...' : ''))); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($opportunity['available_slots']); ?></td>
                                        <td><?php echo htmlspecialchars(date('M j, Y', strtotime($opportunity['application_deadline']))); ?>
                                        </td>
                                        <td>
                                            <span
                                                class="badge rounded-pill bg-<?php echo $opportunity['status'] === 'open' ? 'success' : 'secondary'; ?> status-badge">
                                                <?php echo htmlspecialchars(ucfirst($opportunity['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted"> No opportunities posted yet</h5>
                        <p class="text-muted">Click "Create Opportunity" to post your first opportunity</p>
                        <a href="COpportunities.php?create=true" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i>Create Opportunity
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../../help-center.php" class="text-white fw-bold">Help Center</a>
            <a href="../../terms.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../../contact.php" class="text-white fw-bold">Contact Support: 0700234362</a>
        </div>
    </footer>

    <!-- Include Bootstrap JS for alert dismissal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../Javascript/COpportunities.js?v=<?= time() ?>"></script>
</body>
</html>