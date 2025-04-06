<?php
session_start(); // Start the session
require_once '../../db.php'; // Include the database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Fetch company ID based on logged-in user
try {
    $query = "SELECT company_id FROM users WHERE company_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Check if user was found
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $company_id = $user['company_id']; // Get company_id associated with the user
    } else {
        echo json_encode(['error' => 'User not found or no associated company.']);
        exit;
    }
} catch (PDOException $e) {
    // Log the detailed error message
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error occurred while fetching company ID: ' . $e->getMessage()]);
    exit;
}

// Handle Profile and Password Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Reset Password Process
    if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['error' => 'New passwords do not match.']);
            exit;
        }

        try {
            // Fetch the current password for the logged-in user
            $query = "SELECT password FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                echo json_encode(['error' => 'Current password is incorrect.']);
                exit;
            }

            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindValue(1, $hashedPassword, PDO::PARAM_STR);
            $updateStmt->bindValue(2, $user_id, PDO::PARAM_INT);
            $updateStmt->execute();

            echo json_encode(['success' => 'Password updated successfully.']);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            echo json_encode(['error' => 'Database error occurred.']);
            exit;
        }
    }
    exit;
}

// Fetch company details based on company_id
try {
    $query = "SELECT company_name, location, email FROM companies WHERE company_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $company_id, PDO::PARAM_INT);
    $stmt->execute();

    // Check if company details were found
    if ($stmt->rowCount() > 0) {
        $company = $stmt->fetch(PDO::FETCH_ASSOC);
        $companyDetails = json_encode($company);
    } else {
        $companyDetails = json_encode(['error' => 'Company details not found.']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $companyDetails = json_encode(['error' => 'Database error occurred.']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile & Settings - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/CProfile.css">
</head>

<body class="bg-gray-100 d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-white" href="CHome.php">🏢 AttachME - Profile Settings</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="CHome.php" class="nav-link text-white fw-bold fs-5 active">🏠
                        Dashboard</a></li>
                <li class="nav-item"><a href="COpportunities.php" class="nav-link text-white fw-bold fs-5">📢
                        Opportunities</a></li>
                <li class="nav-item"><a href="CTrack.php" class="nav-link text-white fw-bold fs-5">📄
                        Applications</a></li>
                <li class="nav-item"><a href="CNotifications.php" class="nav-link text-white fw-bold fs-5">💬
                        Messages</a></li>
                <li class="nav-item"><a href="CProfile.php" class="nav-link text-white fw-bold fs-5">🏢
                        Profile</a></li>
            </ul>
        </div>
    </nav>

    <div class="container p-5 flex-grow-1">
        <h4 class="fw-bold text-primary">👤 Profile & Settings</h4>
        <p class="text-muted">Manage your company details and security settings.</p>

        <form id="profileSettingsForm" method="POST" action="CProfile.php">
            <fieldset class="card p-4 mb-4">
                <legend class="fw-bold">Company Information</legend>
                <div class="mb-3">
                    <label for="companyName" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="companyName" name="companyName"
                        placeholder="Enter company name" required readonly>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="Enter location"
                        required readonly>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required
                        readonly>
                </div>
            </fieldset>

            <fieldset class="card p-4">
                <legend class="fw-bold">Security Settings</legend>
                <div class="mb-3">
                    <label for="currentPassword" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="currentPassword" name="currentPassword"
                        placeholder="Enter current password" required>
                </div>
                <div class="mb-3">
                    <label for="newPassword" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword"
                        placeholder="Enter new password" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                        placeholder="Confirm new password" required>
                </div>
            </fieldset>

            <button type="submit" class="btn btn-primary w-100 mt-3">Save All Changes</button>
        </form>
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const companyDetails = <?php echo $companyDetails; ?>;

            if (companyDetails && companyDetails.company_name) {
                document.getElementById("companyName").value = companyDetails.company_name;
                document.getElementById("location").value = companyDetails.location;
                document.getElementById("email").value = companyDetails.email;
            }
        });
    </script>
</body>

</html>