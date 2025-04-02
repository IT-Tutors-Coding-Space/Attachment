<?php
// COpportunitiess.php
session_start();
require_once "../../db.php";

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["action"]) && $_POST["action"] === "createOpportunity") {
        try {
            // Check if database connection is valid
            if (!$conn) {
                throw new Exception("Database connection is not established.");
            }

            if (empty($_POST["title"])) {
                echo json_encode(["success" => false, "message" => "Title is required."]);
                exit();
            }
            if (empty($_POST["deadline"])) {
                echo json_encode(["success" => false, "message" => "Deadline is required."]);
                exit();
            }
            if (empty($_POST["description"])) {
                echo json_encode(["success" => false, "message" => "Description is required."]);
                exit();
            }
            if (empty($_POST["positions"])) {
                echo json_encode(["success" => false, "message" => "Positions is required."]);
                exit();
            }

            $title = htmlspecialchars($_POST["title"]); // Sanitize input
            $deadline = htmlspecialchars($_POST["deadline"]); // Sanitize input
            $description = htmlspecialchars($_POST["description"]); // Sanitize input
            $positions = intval($_POST["positions"]); // Ensure integer
            $company_id = $_SESSION["company_id"]; // Get company ID from session

            $conn->beginTransaction();

            $sql = "INSERT INTO opportunities (company_id, title, description, available_slots, application_deadline, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 'Open', NOW(), NOW())";
            $stmt = $conn->prepare($sql);

            // Log the query and parameters for debugging
            error_log("Executing SQL: $sql with parameters: " . json_encode([$company_id, $title, $description, $positions, $deadline]));

            $stmt->execute([$company_id, $title, $description, $positions, $deadline]);

            // Debugging: Check for SQL errors
            if ($stmt->errorCode() !== "00000") {
                throw new PDOException("SQL Error: " . implode(", ", $stmt->errorInfo()));
            }

            $conn->commit();

            http_response_code(200); // OK
            echo json_encode(["success" => true, "message" => "Opportunity created successfully"]);
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            // Log error for debugging
            error_log("Database Error: " . $e->getMessage());
            http_response_code(500); // Internal Server Error
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        } catch (Exception $e) {
            // Log general errors
            error_log("General Error: " . $e->getMessage());
            http_response_code(500); // Internal Server Error
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
        exit();
    }

    // ...existing code...
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opportunity Management - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/company.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">🏢 AttachME - Opportunity Management</h2>
            <a href="../Company/CHome.php" class="btn btn-outline-light">🏠 Dashboard</a>
        </div>
    </nav>

    <div class="container p-5 flex-grow-1">
        <h4 class="fw-bold text-primary">Manage Your Opportunities</h4>
        <p class="text-muted">View, post, and manage internship opportunities seamlessly.</p>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createOpportunityModal">
            <i class="fa fa-plus"></i> Post New Opportunity
        </button>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Deadline</th>
                        <th>Applicants</th>
                        <th>Status</th>
                        <th>Actions</th>
                </thead>
                <tbody id="opportunityTable">
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="createOpportunityModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Post/Edit Opportunity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="opportunityForm" method="post" action="COpportunities.php">
                        <div class="mb-3">

                            <label class="form-label">Opportunity Title</label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="Enter job title" required minlength="5">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Application Deadline</label>
                            <input name="deadline" type="date" class="form-control" id="deadline" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="description" rows="3" placeholder="Provide a brief description" required minlength="10"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Number of Positions</label>
                            <input name="deadline" type="number" class="form-control" id="positions" min="1" placeholder="Enter available positions" required min="1">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Opportunity</button>
                    </form>


                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="help-center.html" class="text-white fw-bold">Help Center</a>
            <a href="terms.html" class="text-white fw-bold">Terms of Service</a>
            <a href="contact.html" class="text-white fw-bold">Contact Support: 0700234362</a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../Javasript/COpportunities.js"></script>
</body>
</html>