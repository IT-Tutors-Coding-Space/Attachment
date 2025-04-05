<?php
require_once "../../db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location:  ../SignUps/SLogin.php");
    exit();
}

$student_id = $_SESSION["user_id"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service & Privacy Policy - AttachME</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/terms-of-service.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME - Terms & Privacy</h2>
            <a href="../Students/SDashboard.php" class="btn btn-outline-light">🏠 Home</a>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="text-center mb-4">
            <h1 class="text-3xl fw-bold">Terms of Service & Privacy Policy</h1>
            <p class="text-muted">Please read our terms and privacy statements carefully before using AttachME.</p>
        </header>

        <!-- Terms & Privacy Sections -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
            <h5 class="fw-bold text-primary">1. Acceptance of Terms</h5>
            <p>By accessing and using AttachME, you agree to comply with these terms. If you do not agree, please discontinue use.</p>
            
            <h5 class="fw-bold text-primary">2. User Responsibilities</h5>
            <ul>
                <li>Provide accurate and up-to-date information.</li>
                <li>Use the platform responsibly and ethically.</li>
                <li>Respect other users and their data privacy.</li>
                <li>Ensure that content shared does not violate any intellectual property rights.</li>
            </ul>
            
            <h5 class="fw-bold text-primary">3. Privacy Policy</h5>
            <p>Your privacy is important to us. We collect, use, and store your data securely.</p>
            <ul>
                <li><strong>Data Collection:</strong> We collect personal details like name, email, and user activity for platform functionality.</li>
                <li><strong>Data Usage:</strong> Your data is used for improving services, communication, and ensuring security.</li>
                <li><strong>Third-Party Sharing:</strong> We do not sell or share your personal data with third parties without your consent.</li>
                <li><strong>Security Measures:</strong> We use encryption and authentication to protect user data.</li>
                <li><strong>Account Control:</strong> Users can modify or delete their account information at any time.</li>
                <li><strong>Cookies & Tracking:</strong> AttachME uses cookies to enhance user experience, track website traffic, and improve functionality.</li>
                <li><strong>Data Retention:</strong> We retain user data only for as long as necessary for legal and business purposes.</li>
            </ul>
            
            <h5 class="fw-bold text-primary">4. Prohibited Activities</h5>
            <ul>
                <li>Engaging in fraudulent activities.</li>
                <li>Harassing or threatening other users.</li>
                <li>Attempting to breach the platform’s security.</li>
                <li>Uploading harmful or malicious content.</li>
                <li>Creating multiple fake accounts to abuse system features.</li>
            </ul>
            
            <h5 class="fw-bold text-primary">5. Termination of Use</h5>
            <p>AttachME reserves the right to suspend or terminate accounts that violate these terms without prior notice.</p>
            <p>Users who repeatedly engage in misconduct may be permanently banned from using the platform.</p>

            <h5 class="fw-bold text-primary">6. Limitation of Liability</h5>
            <p>AttachME is not responsible for any damages, losses, or legal issues arising from misuse of the platform.</p>
            <p>Users agree to use AttachME at their own risk and acknowledge that the platform provides services "as is" without warranties.</p>

            <h5 class="fw-bold text-primary">7. Updates & Modifications</h5>
            <p>We may update these terms and privacy policies from time to time. Continued use after updates constitutes acceptance.</p>
            <p>We will notify users via email or a public announcement for significant changes.</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../Help Center.html" class="text-white fw-bold">Help Center</a>
            <a href="../Students/Terms of servive.html" class="text-white fw-bold">Terms & Privacy</a>
            <a href="../Students/Contact Support.html" class="text-white fw-bold">Contact Support:</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="js/terms-of-service.js"></script>
</body>
</html>
