<?php
// Include database connection file
require_once('../../db.php');

// Start session
session_start();
// if (isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
//     // User is logged in and is a student
//     header("Location: ../SignUps/Slogin.php");
//     exit();
// }
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Settings - AttachME</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/student-styles.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME - Student Portal</h2>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="../Students/SDashboard.php" class="nav-link text-white fw-bold fs-5 active">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="../Students/SAbout.php" class="nav-link text-white fw-bold fs-5 active">📖 About Us</a></li>

                <li class="nav-item"><a href="../Students/SBrowse.php" class="nav-link text-white fw-bold fs-5">🔍 Browse Opportunities</a></li>
                <li class="nav-item"><a href="../Students/SApplicationSubmission.php" class="nav-link text-white fw-bold fs-5">📄 My Applications</a></li>
                <li class="nav-item"><a href="../Students/SNotifications.php" class="nav-link text-white fw-bold fs-5">💬 Messages</a></li>
                <li class="nav-item"><a href="../Students/SProfile.php" class="nav-link text-white fw-bold fs-5">👤 Profile</a></li>
                <li class="nav-item"><a href="../Students/SSettings.php" class="nav-link text-white fw-bold fs-5">⚙️ Settings</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h1 class="text-3xl fw-bold">Account Settings</h1>
            <p class="text-muted">Manage your account preferences, security, and notifications.</p>
        </header>

        <div class="row g-4">
            <!-- Account Settings -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                    <h5 class="fw-bold fs-5 mb-3">Profile Settings</h5>
                    <label class="fw-bold">Full Name</label>
                    <input type="text" class="form-control mb-3" id="studentName" value="John Marie">
                    <label class="fw-bold">Email Address</label>
                    <input type="email" class="form-control mb-3" id="studentEmail" value="johnm@gmail.com">
                    <label class="fw-bold">Change Password</label>
                    <input type="password" class="form-control mb-3" id="studentPassword" placeholder="Enter new password">
                    <button class="btn btn-primary w-100" id="saveProfile">Save Changes</button>
                </div>
            </div>
            
            <!-- Security Settings -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                    <h5 class="fw-bold fs-5 mb-3">Security Settings</h5>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="enable2FA">
                        <label class="form-check-label fw-bold" for="enable2FA">Enable Two-Factor Authentication</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="sessionTimeout">
                        <label class="form-check-label fw-bold" for="sessionTimeout">Enable Session Timeout</label>
                    </div>
                    <a href="SignUps\logout.php" class="btn btn-danger w-100" id="logout">Log Out</a> 
                </div>
            </div>
        </div>

        <!-- Notification Preferences -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg mt-4">
            <h5 class="fw-bold fs-5 mb-3">Notification Preferences</h5>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                <label class="form-check-label" for="emailNotifications">Receive email notifications</label>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="smsNotifications">
                <label class="form-check-label" for="smsNotifications">Receive SMS notifications</label>
            </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../Help Center.html" class="text-white fw-bold">Help Center</a>
            <a href="../Students/Terms of servive.html" class="text-white fw-bold">Terms of Service</a>
            <a href="tel:+254700234362" class="text-white fw-bold">Contact Support:</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javasript/SSettings.js"></script>
</body>
</html>
