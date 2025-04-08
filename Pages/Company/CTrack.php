<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../db.php';

// Enhanced error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in. Session data: " . print_r($_SESSION, true));
    header("Location: /login.php");
    exit;
}

// Debug: Log current session
error_log("Current session data: " . print_r($_SESSION, true));

// Fetch company_id if not set in session
if (!isset($_SESSION['company_id'])) {
    try {
        // Verify database connection
        if (!$conn) {
            throw new Exception("Database connection failed. Error: " . print_r($conn->errorInfo(), true));
        }

        // Debug: Log the query being executed
        error_log("Fetching company_id for user_id: " . $_SESSION['user_id']);

        $query = "SELECT company_id FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . print_r($conn->errorInfo(), true));
        }

        $stmt->bindValue(1, $_SESSION['user_id'], PDO::PARAM_INT);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . print_r($stmt->errorInfo(), true));
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Log query result
        error_log("Query result: " . print_r($result, true));

        if ($result && isset($result['company_id'])) {
            $_SESSION['company_id'] = $result['company_id'];
            error_log("Set company_id in session: " . $result['company_id']);
        } else {
            throw new Exception("No company found for user. User ID: " . $_SESSION['user_id']);
        }
    } catch (Exception $e) {
        error_log("Error fetching company_id: " . $e->getMessage());

        // User-friendly error page
        die('
        <!DOCTYPE html>
        <html>
        <head>
            <title>Error</title>
            <style>
                .error-container {
                    max-width: 600px;
                    margin: 50px auto;
                    padding: 20px;
                    border: 1px solid #e0e0e0;
                    border-radius: 5px;
                    background-color: #f9f9f9;
                }
                .error-title {
                    color: #d9534f;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h2 class="error-title">System Error</h2>
                <p>We encountered an issue verifying your company information.</p>
                <p>Please contact support at 0700234362 and provide this reference: USER_' . $_SESSION['user_id'] . '</p>
                <p><small>Technical details have been logged.</small></p>
            </div>
        </body>
        </html>');
    }
}

$company_id = $_SESSION['company_id'];
error_log("Current company_id: " . $company_id);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || !isset($data['status'])) {
        http_response_code(400);
        die(json_encode(['error' => 'Missing required fields']));
    }

    try {
        // Verify the application belongs to this company
        $verifyQuery = "SELECT o.company_id 
                        FROM applications app
                        JOIN opportunities o ON app.opportunity_id = o.id
                        WHERE app.id = ?";

        $verifyStmt = $conn->prepare($verifyQuery);
        $verifyStmt->bindValue(1, $data['id'], PDO::PARAM_INT);
        $verifyStmt->execute();

        $result = $verifyStmt->fetch(PDO::FETCH_ASSOC);

        if (!$result || $result['company_id'] != $company_id) {
            http_response_code(403);
            die(json_encode(['error' => 'Not authorized to update this application']));
        }

        // Update the application
        $updateQuery = "UPDATE applications SET status = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindValue(1, $data['status'], PDO::PARAM_STR);
        $updateStmt->bindValue(2, $data['id'], PDO::PARAM_INT);
        $updateStmt->execute();

        echo json_encode(['success' => true, 'message' => 'Application updated successfully']);
        exit;

    } catch (PDOException $e) {
        http_response_code(500);
        die(json_encode(['error' => 'Database error: ' . $e->getMessage()]));
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Applications - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-accepted {
            background-color: #28a745;
            color: #fff;
        }

        .badge-rejected {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
</head>

<body class="bg-gray-100 d-flex flex-column min-vh-100">
    <!-- Navigation and HTML content remains the same as before -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../Javasript/CTrack.js"></script>
</body>

</html>