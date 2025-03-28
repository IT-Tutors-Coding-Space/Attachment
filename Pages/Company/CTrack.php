<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Student Applications</title>
    <link rel="stylesheet" href="CTrack.css">
</head>

<body>
    <div class="container">
        <h2>Student Applications Tracking</h2>

        <input type="text" id="searchInput" placeholder="Search by name...">
        <select id="filterStatus">
            <option value="all">All</option>
            <option value="pending">Pending</option>
            <option value="accepted">Accepted</option>
            <option value="rejected">Rejected</option>
        </select>

        <table>
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Student Name</th>
                    <th onclick="sortTable(1)">Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="applicationTable">
                <!-- Data will be dynamically inserted -->
            </tbody>
        </table>
    </div>

    <div id="applicationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Application Details</h3>
            <p id="studentDetails"></p>
            <button class="status-btn" data-status="accepted">Accept</button>
            <button class="status-btn" data-status="rejected">Reject</button>
        </div>
    </div>

    <script src="CTrack.js"></script>
    <footer>
        <p>&copy; 2025 AttachME. All rights reserved.</p>
    </footer>
</body>

</html>
=======
    <title>Student Applications - AttachME</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/company.css">
</head>

<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid">
            <h2 class="text-white fw-bold fs-3">AttachME - Track </h2>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="../Company/CHome.html" class="nav-link text-white fw-bold fs-5 active">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="../Company/COpportunities.html" class="nav-link text-white fw-bold fs-5">📢 Opportunities</a></li>
                <li class="nav-item"><a href="../Company/CTrack.html" class="nav-link text-white fw-bold fs-5">📄 Applications</a></li>
                <li class="nav-item"><a href="../Company/CNotifications.html" class="nav-link text-white fw-bold fs-5">💬 Messages</a></li>
                <li class="nav-item"><a href="../Company/CProfile.html" class="nav-link text-white fw-bold fs-5">🏢 Profile</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <h4 class="fw-bold text-primary">Track Student Applications</h4>
        <p class="text-muted">Search, filter, and manage student applications easily.</p>
        
        <div class="d-flex justify-content-between mb-3">
            <input type="text" id="searchInput" class="form-control w-50" placeholder="Search by student name...">
            <select id="filterStatus" class="form-control w-25">
                <option value="all">All</option>
                <option value="pending">Pending</option>
                <option value="accepted">Accepted</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th onclick="sortTable(0)">Student Name</th>
                        <th onclick="sortTable(1)">Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="applicationTable">
                    <!-- Applications will be dynamically inserted here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Application Details Modal -->
    <div id="applicationModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Application Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="studentDetails"></p>
                    <div class="d-flex justify-content-around">
                        <button class="btn btn-success status-btn" data-status="accepted">Accept</button>
                        <button class="btn btn-danger status-btn" data-status="rejected">Reject</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="help-center.html" class="text-white fw-bold">Help Center</a>
            <a href="terms.html" class="text-white fw-bold">Terms of Service</a>
            <a href="contact.html" class="text-white fw-bold">Contact Support: 0700234362</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javasript/CTrack.js"></script>
</body>

</html>
>>>>>>> origin/main
