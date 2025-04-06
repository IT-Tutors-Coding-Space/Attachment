<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - AttachME</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/help-center.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME - Help Center</h2>
            <a href="../Pages/Company/CHome.php" class="btn btn-outline-light">🏠 Home</a>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="text-center mb-4">
            <h1 class="text-3xl fw-bold">How Can We Help You?</h1>
            <p class="text-muted">Find answers to frequently asked questions and get support.</p>
        </header>

        <!-- Search Bar -->
        <div class="input-group mb-4">
            <input type="text" class="form-control" id="searchHelp" placeholder="🔍 Search for help topics...">
            <button class="btn btn-primary">Search</button>
        </div>

        <!-- Help Topics -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                    <h5 class="fw-bold">👤 Account Management</h5>
                    <p>Learn how to manage your profile, update your details, and change your password.</p>
                    <a href="#" class="text-primary">Read More</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                    <h5 class="fw-bold">📩 Applications & Opportunities</h5>
                    <p>Find out how to browse opportunities, submit applications, and track progress.</p>
                    <a href="#" class="text-primary">Read More</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                    <h5 class="fw-bold">🔒 Security & Privacy</h5>
                    <p>Learn how we protect your data and how to enable security features.</p>
                    <a href="#" class="text-primary">Read More</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                    <h5 class="fw-bold">📞 Contact Support</h5>
                    <p>Need more help? Get in touch with our support team.</p>
                    <a href="contact.html" class="text-primary">Contact Us</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../Pages/Company/Help Center.php" class="text-white fw-bold">Help Center</a>
            <a href="../Pages/Company/Terms of service.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../Pages/Company/Contact Support.php" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="js/help-center.js"></script>
</body>
</html>