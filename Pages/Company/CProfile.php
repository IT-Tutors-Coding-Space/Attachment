<?php
require_once('../../db.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: ../../SignUps/Clogin.php");
    exit();
}

$company_id = $_SESSION['user_id'];

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['current_password'])) {
    try {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM companies WHERE company_id = ?");
        $stmt->execute([$company_id]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($_POST['current_password'], $company['password'])) {
            throw new Exception("Current password is incorrect");
        }

        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            throw new Exception("New passwords do not match");
        }

        // Update password
        $hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE companies SET password = ? WHERE company_id = ?");
        $stmt->execute([$hashed_password, $company_id]);

        $success_message = "Password updated successfully!";
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    try {
        $conn->beginTransaction();

        // Delete company and related data
        $stmt = $conn->prepare("DELETE FROM companies WHERE company_id = ?");
        $stmt->execute([$company_id]);

        $conn->commit();

        // Logout and redirect
        session_destroy();
        header("Location: ../../SignUps/Clogin.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $error_message = "Failed to delete account: " . $e->getMessage();
    }
}

// Fetch company profile data
try {
    $stmt = $conn->prepare("SELECT * FROM companies WHERE company_id = ?");
    $stmt->execute([$company_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching profile data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .profile-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .danger-zone {
            border-left: 4px solid #dc3545;
        }

        .readonly-field {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
    </style>
</head>

<body class="bg-gray-100 d-flex flex-column min-vh-100">

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME - Company Portal</h2>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="CHome.php" class="nav-link text-white fw-bold fs-5"> Dashboard</a></li>
                <li class="nav-item"><a href="COpportunities.php" class="nav-link text-white fw-bold fs-5">
                        Opportunities</a></li>
                <li class="nav-item"><a href="CTrack.php" class="nav-link text-white fw-bold fs-5"> Applications</a>
                </li>
                <li class="nav-item"><a href="CNotifications.php" class="nav-link text-white fw-bold fs-5">
                        Messages</a></li>
                <li class="nav-item"><a href="CProfile.php" class="nav-link text-white fw-bold fs-5 active">
                        Profile</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h1 class="text-3xl fw-bold">Company Profile</h1>
        </header>

        <!-- Display messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Profile Section -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
            <div class="row">
                <!-- Profile Information Column -->
                <div class="col-md-8">
                    <!-- Company Information Card -->
                    <div class="card mb-4 profile-card">
                        <div class="card-body">
                            <h4 class="card-title fw-bold mb-4">
                                <i class="fas fa-building me-2"></i>Company Information
                            </h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-muted">Company Name</label>
                                    <div class="form-control readonly-field">
                                        <?php echo htmlspecialchars($profile['company_name']); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-muted">Email</label>
                                    <div class="form-control readonly-field">
                                        <?php echo htmlspecialchars($profile['email']); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-muted">Location</label>
                                    <div class="form-control readonly-field">
                                        <?php echo htmlspecialchars($profile['location']); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-muted">Industry</label>
                                    <div class="form-control readonly-field">
                                        <?php echo htmlspecialchars($profile['industry']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Password Settings Card -->
                    <div class="card mb-4 profile-card">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">
                                <i class="fas fa-lock me-2"></i>Password Settings
                            </h5>
                            <form id="passwordForm" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" class="form-control" name="current_password" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" name="new_password" required
                                            minlength="8">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Update Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Actions Card -->
                    <div class="card border-danger danger-zone">
                        <div class="card-header bg-danger text-white">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="fw-bold">Delete Account</h6>
                                    <p class="small text-muted mb-0">
                                        This will permanently remove your company account and all related data.
                                        This action cannot be undone.
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button class="btn btn-outline-danger" id="deleteAccountBtn">
                                        <i class="fas fa-trash-alt me-1"></i>Delete Account
                                    </button>
                                </div>
                            </div>

                            <div class="row align-items-center mt-3 pt-3 border-top">
                                <div class="col-md-8">
                                    <h6 class="fw-bold">Logout</h6>
                                    <p class="small text-muted mb-0">
                                        Securely sign out of your account from this device.
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="../../auth/logout.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../../help-center.php" class="text-white fw-bold">Help Center</a>
            <a href="../../terms.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../../contact.php" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javascript/CProfile.js?v=<?= time() ?>"></script>
</body>

</html>
