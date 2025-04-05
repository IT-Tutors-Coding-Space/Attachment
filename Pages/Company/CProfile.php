<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

require_once '../../db.php'; // Include your database connection file

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $companyName = filter_input(INPUT_POST, 'companyName', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); // email is now the contact info.
    $currentPassword = filter_input(INPUT_POST, 'currentPassword', FILTER_SANITIZE_STRING);
    $newPassword = filter_input(INPUT_POST, 'newPassword', FILTER_SANITIZE_STRING);
    $confirmPassword = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_STRING);

    // Initialize response array
    $response = [];

    // Update company information
    if ($companyName && $location && $email) { //email is the contact now.
        $updateProfileQuery = "UPDATE companies SET company_name = ?, location = ?, email = ? WHERE user_id = ?"; // email is now the contact info.
        if ($stmt = $conn->prepare($updateProfileQuery)) {
            $stmt->bind_param('sssi', $companyName, $location, $email, $user_id); // email is now the contact info.
            if ($stmt->execute()) {
                $response['success'] = 'Profile information updated successfully.';
            } else {
                error_log("Database error (profile update): " . $stmt->error);
                $response['error'] = 'Failed to update profile information.';
            }
            $stmt->close();
        } else {
            error_log("Database error (prepare profile update): " . $conn->error);
            $response['error'] = 'Database error: Unable to prepare statement.';
        }
    }

    // Update password
    if ($currentPassword && $newPassword && $confirmPassword) {
        if ($newPassword === $confirmPassword) {
            // Verify current password
            $passwordQuery = "SELECT password FROM users WHERE id = ?";
            if ($stmt = $conn->prepare($passwordQuery)) {
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $stmt->bind_result($hashedPassword);
                if ($stmt->fetch() && password_verify($currentPassword, $hashedPassword)) {
                    // Update password
                    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updatePasswordQuery = "UPDATE users SET password = ? WHERE id = ?";
                    if ($updateStmt = $conn->prepare($updatePasswordQuery)) {
                        $updateStmt->bind_param('si', $newHashedPassword, $user_id);
                        if ($updateStmt->execute()) {
                            $response['success'] = 'Password updated successfully.';
                        } else {
                            error_log("Database error (password update): " . $updateStmt->error);
                            $response['error'] = 'Failed to update password.';
                        }
                        $updateStmt->close();
                    } else {
                        error_log("Database error (prepare password update): " . $conn->error);
                        $response['error'] = 'Database error: Unable to prepare statement.';
                    }
                } else {
                    $response['error'] = 'Current password is incorrect.';
                }
                $stmt->close();
            } else {
                error_log("Database error (prepare password verification): " . $conn->error);
                $response['error'] = 'Database error: Unable to prepare statement.';
            }
        } else {
            $response['error'] = 'New password and confirmation do not match.';
        }
    }

    // Close the database connection
    $conn->close();

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
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
    <link rel="stylesheet" href="css/company.css">
</head>

<body class="bg-gray-100 d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-white" href="../Company/CHome.html">🏢 AttachME - Profile Settings</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="../Company/CHome.html" class="nav-link text-white fw-bold fs-5 active">🏠
                        Dashboard</a></li>
                <li class="nav-item"><a href="../Company/COpportunities.html"
                        class="nav-link text-white fw-bold fs-5">📢 Opportunities</a></li>
                <li class="nav-item"><a href="../Company/CTrack.html" class="nav-link text-white fw-bold fs-5">📄
                        Applications</a></li>
                <li class="nav-item"><a href="../Company/CNotifications.html"
                        class="nav-link text-white fw-bold fs-5">💬 Messages</a></li>
                <li class="nav-item"><a href="../Company/CProfile.html" class="nav-link text-white fw-bold fs-5">🏢
                        Profile</a></li>
            </ul>
        </div>
    </nav>

    <div class="container p-5 flex-grow-1">
        <h4 class="fw-bold text-primary">👤 Profile & Settings</h4>
        <p class="text-muted">Manage your company details and security settings.</p>

        <form id="profileSettingsForm" method="POST" action="../Pages/Company/CProfile.php">
            <fieldset class="card p-4 mb-4">
                <legend class="fw-bold">Company Information</legend>
                <div class="mb-3">
                    <label for="companyName" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="companyName" name="companyName"
                        placeholder="Enter company name" required>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="Enter location"
                        required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required>
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
    <script src="../../Javasript/CProfile.js"></script>
</body>

</html>