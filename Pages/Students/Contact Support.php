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
    <title>Contact Support - AttachME</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/contact-support.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME - Contact Support</h2>
            <a href="../Students/SDashboard.php" class="btn btn-outline-light">🏠 Home</a>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="text-center mb-4">
            <h1 class="text-3xl fw-bold">Get in Touch with Us</h1>
            <p class="text-muted">Have questions or need help? Contact our support team and we’ll assist you as soon as possible.</p>
        </header>

        <!-- Contact Information -->
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                    <h5 class="fw-bold">📧 Email Support</h5>
                    <p><a href="mailto:support@attachme.com" class="text-primary fw-bold">support@attachme.com</a></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                    <h5 class="fw-bold">📞 Phone Support</h5>
                    <p><a href="tel:+254700234362" class="text-primary fw-bold">0700234362</a></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                    <h5 class="fw-bold">💬 Live Chat</h5>
                    <p><a href="#" class="text-primary fw-bold">Chat with an Agent</a></p>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg mt-4">
            <h5 class="fw-bold mb-3">Send Us a Message</h5>
            <form id="contactForm">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter your full name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" placeholder="Enter your email address" required>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" id="subject" placeholder="Enter the subject" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Your Message</label>
                    <textarea class="form-control" id="message" rows="4" placeholder="Describe your issue or inquiry..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../Students/Help Center.php" class="text-white fw-bold">Help Center</a>
            <a href="../Students/Terms of service.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../Students/Contact Support.php" class="text-white fw-bold">Contact Support:</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javascript/Contact Support.js"></script>
</body>
</html>
