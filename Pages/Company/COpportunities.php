<?php
session_start();
require_once "../../db.php";

// Check if the user is authenticated and is a company
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    http_response_code(403); // Forbidden
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input fields
    if (empty($_POST["title"]) || empty($_POST["description"]) || empty($_POST["requirements"]) || empty($_POST["available_slots"]) || empty($_POST["application_deadline"])) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    // Validate application deadline (must be in the future)
    $deadline = $_POST["application_deadline"];
    if (strtotime($deadline) <= time()) {
        echo json_encode(["success" => false, "message" => "Application deadline must be in the future."]);
        exit();
    }

    // Validate available slots (must be a positive integer)
    $slots = $_POST["available_slots"];
    if (!ctype_digit($slots) || intval($slots) <= 0) {
        echo json_encode(["success" => false, "message" => "Available slots must be a positive integer."]);
        exit();
    }

    // Validate Description and Requirements (must be text)
    $description = $_POST["description"];
    $requirements = $_POST["requirements"];
    if (!is_string($description) || !is_string($requirements)) {
        echo json_encode(["success" => false, "message" => "Description and requirements must be text."]);
        exit();
    }

    // Insert the opportunity posting
    $companyId = $_SESSION["user_id"]; // Assuming the company ID is stored in the session
    $status = "open"; // Set the initial status to 'open'

    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("INSERT INTO opportunities (company_id, title, description, requirements, available_slots, application_deadline, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$companyId, $_POST["title"], $_POST["description"], $_POST["requirements"], $_POST["available_slots"], $_POST["application_deadline"], $status]);

        $conn->commit();

        echo json_encode(["success" => true, "message" => "Opportunity posted successfully!"]);
        exit();
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Opportunity - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/COpportunities.css">
</head>

<body class="bg-gray-100 d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-white" href="CHome.php">🏢 AttachME - Post Opportunity</a>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="CHome.php" class="nav-link text-white fw-bold fs-5">🏠
                        Dashboard</a></li>
                <li class="nav-item"><a href="COpportunities.php"
                        class="nav-link text-white fw-bold fs-5 active">📢 Opportunities</a></li>
                <li class="nav-item"><a href="CTrack.php" class="nav-link text-white fw-bold fs-5">📄
                        Applications</a></li>
                <li class="nav-item"><a href="CNotifications.php"
                        class="nav-link text-white fw-bold fs-5">💬 Messages</a></li>
                <li class="nav-item"><a href="CProfile.php" class="nav-link text-white fw-bold fs-5">🏢
                        Profile</a></li>
            </ul>
        </div>
    </nav>
    <div class="container p-5 flex-grow-1 d-flex justify-content-center align-items-center">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg w-100" style="max-width: 500px;">
            <h5 class="fw-bold text-center text-primary mb-3">Post a New Opportunity</h5>
            <form id="opportunityForm" method="POST" action="COpportunities.php">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="requirements" class="form-label">Requirements</label>
                    <textarea class="form-control" id="requirements" name="requirements" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="available_slots" class="form-label">Available Slots</label>
                    <input type="number" class="form-control" id="available_slots" name="available_slots" required>
                </div>
                <div class="mb-3">
                    <label for="application_deadline" class="form-label">Application Deadline</label>
                    <input type="date" class="form-control" id="application_deadline" name="application_deadline"
                        required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Post Opportunity</button>
            </form>
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
    <script src="../Javasript/COpportunities.js"></script>
</body>

</html>