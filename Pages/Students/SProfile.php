<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - AttachME</title>
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
            <h1 class="text-3xl fw-bold">My Profile</h1>
            <button class="btn btn-primary" id="editProfileBtn">Edit Profile</button>
        </header>

        <!-- Profile Section -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="images/default-profile.png" alt="Profile Picture" class="img-fluid rounded-circle mb-3" id="profileImage" width="150">
                    <input type="file" class="form-control" id="uploadProfileImage" hidden>
                    <button class="btn btn-outline-secondary" id="changeProfileImage">Change Photo</button>
                </div>
                <div class="col-md-8">
                    <h5 class="fw-bold">Full Name:</h5>
                    <p id="profileName">John Marie</p>
                    <h5 class="fw-bold">Email:</h5>
                    <p id="profileEmail">johnm@gmail.com</p>
                    <h5 class="fw-bold">Course:</h5>
                    <p id="profileCourse">BSc. Computer Science</p>
                    <h5 class="fw-bold">Year of Study:</h5>
                    <p id="profileYear">3rd Year</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../Help Center.html" class="text-white fw-bold">Help Center</a>
            <a href="../Students/Terms of servive.html" class="text-white fw-bold">Terms of Service</a>
            <a href="tel:+254700234362" class="text-white fw-bold">Contact Support: 0700234362</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javasript/SProfile.js"></script>
</body>
</html>
