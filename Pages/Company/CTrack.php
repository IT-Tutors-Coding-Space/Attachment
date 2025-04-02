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
            <a class="navbar-brand fw-bold text-white" href="../Company/CHome.html"> AttachME - Profile Settings</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item">< href="../Company/CHome.html"
                        class="nav-link text-white fw-bold fs-5 active"> Dashboard</a></li>
                <li class="nav-item"><a href="../Company/COpportunities.html"
                        class="nav-link text-white fw-bold fs-5">Opportunities</a></li>
                <li class="nav-item"><a href="../Company/CTrack.html"
                        class="nav-link text-white fw-bold fs-5">Applications</a></li>
                <li class="nav-item"><a href="../Company/CNotifications.html"
                        class="nav-link text-white fw-bold fs-5">Messages</a></li>
                <li class="nav-item">< href="../Company/CProfile.html"
                        class="nav-link text-white fw-bold fs-5"> Profile</a></li>
            </ul>
        </div>
        </div>
    </nav>

    <div class="container p-5 flex-grow-1">
        <h4 class="fw-bold text-primary">👤 Profile & Settings</h4>
        <p class="text-muted">Manage your company details and security settings.</p>

        <div class="card p-4 mb-4">
            <h5 class="fw-bold">Company Information</h5>
            <input type="text" class="form-control mb-2" id="companyName" placeholder="Company Name">
            <input type="text" class="form-control mb-2" id="location" placeholder="Location">
            <input type="text" class="form-control mb-2" id="industry" placeholder="Industry">
            <input type="text" class="form-control mb-2" id="contact" placeholder="Contact Info">
            <button class="btn btn-primary w-100" id="saveProfile">Save Changes</button>
        </div>

        <div class="card p-4">
            <h5 class="fw-bold">Security Settings</h5>
            <input type="password" class="form-control mb-2" id="currentPassword" placeholder="Current Password">
            <input type="password" class="form-control mb-2" id="newPassword" placeholder="New Password">
            <input type="password" class="form-control mb-2" id="confirmPassword" placeholder="Confirm New Password">
            <button class="btn btn-danger w-100" id="updatePassword">Update Password</button>
        </div>
        <div id="profileUpdateMessage" class="mt-3"></div>
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
