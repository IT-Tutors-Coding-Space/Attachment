<?php
require_once "../../db.php";
session_start();

// Check if the user is logged in
if ($_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/ALogin.php");
    exit();
}
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttachME Admin - Users</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/admin-styles.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME</h2>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="../Admin/AHome.php" class="nav-link text-white fw-bold fs-5">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="../Admin/AUsers.php" class="nav-link text-white fw-bold fs-5 active">👤 Users</a></li>
                <li class="nav-item"><a href="../Admin/ACompanies.php" class="nav-link text-white fw-bold fs-5">🏢 Companies</a></li>
                <li class="nav-item"><a href="../Admin/AOpportunities.php" class="nav-link text-white fw-bold fs-5">📢 Opportunities</a></li>
                <li class="nav-item"><a href="../Admin/AApplications.php" class="nav-link text-white fw-bold fs-5">📄 Applications</a></li>
                <li class="nav-item"><a href="../Admin/AAnalytics.php" class="nav-link text-white fw-bold fs-5">📊 Analytics</a></li>
                <li class="nav-item"><a href="../Admin/ASettings.php" class="nav-link text-white fw-bold fs-5">⚙️ Settings</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h1 class="text-3xl fw-bold">Manage Users</h1>
            <div class="d-flex align-items-center gap-3">
                <select id="roleFilter" class="form-select w-auto" onchange="filterUsers()">
                    <option value="all">All Roles</option>
                    <option value="student">Student</option>
                    <option value="company">Company</option>
                    <option value="admin">Admin</option>
                </select>
                <input type="text" class="form-control w-50" id="searchUsers" placeholder="Search users..." onkeyup="searchUsers()">
                <button class="btn btn-primary fw-bold fs-5" data-bs-toggle="modal" data-bs-target="#addUserModal">+ Add User</button>
            </div>
        </header>

        <!-- Users Table -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
            <h5 class="fw-bold fs-5 mb-3">User List</h5>
            <table class="table table-striped">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                <?php
                    $usersStmt = $conn->query("SELECT * FROM users");
                    while ($user = $usersStmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr data-role='{$user['role']}'>
                                <td>{$user['user_id']}</td>
                                <td>{$user['email']}</td>
                                <td>{$user['role']}</td>
                                <td><span class='badge " . ($user['status'] == 'Active' ? 'bg-success' : 'bg-danger') . "'>{$user['status']}</span></td>
                                <td>
                                <button type='button' class='btn btn-outline-warning btn-sm w-100 mb-1' 
                                    onclick='openEditModal(
                                        \"{$user['user_id']}\",
                                        \"{$user['email']}\",
                                        \"{$user['role']}\",
                                        \"{$user['status']}\"
                                    )'>
                                    Edit
                                </button>
                                <form method='POST' action='deleteUser.php'>
                                    <input type='hidden' name='user_id' value='{$user['user_id']}'>
                                    <button type='submit' class='btn btn-outline-danger btn-sm w-100'>Delete</button>
                                </form>
                                </td>
                              </tr>";
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm" method="POST" action="addUser.php">
                        <div class="mb-3">
                            <label for="userType" class="form-label">Select User Type</label>
                            <select class="form-select" id="userType" name="userType" required>
                                <option value="">Select User Type</option>
                                <option value="student">Student</option>
                                <option value="company">Company</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Proceed to Signup</button>
                    </form>
                    <script>
                        document.getElementById('addUserForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            const userType = document.getElementById('userType').value;
                            const email = document.getElementById('email').value;
                            
                            if (!userType || !email) {
                                alert('Please select a user type and enter an email');
                                return;
                            }
                            
                            this.submit();
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" method="POST" action="editUser.php">
                        <input type="hidden" name="user_id" id="editUserId">
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label">Role</label>
                            <select class="form-select" id="editRole" name="role" required>
                                <option value="student">Student</option>
                                <option value="company">Company</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../Help Center.php" class="text-white fw-bold">Help Center</a>
            <a href="../Admin/Terms of service.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../Admin/Contact Support.php" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchUsers() {
            const input = document.getElementById('searchUsers').value.toLowerCase();
            const rows = document.querySelectorAll('#userTableBody tr');
            rows.forEach(row => {
                const email = row.cells[1].textContent.toLowerCase();
                row.style.display = email.includes(input) ? '' : 'none';
            });
        }

        function filterUsers() {
            const selectedRole = document.getElementById('roleFilter').value;
            const rows = document.querySelectorAll('#userTableBody tr');
            rows.forEach(row => {
                const role = row.getAttribute('data-role');
                if (selectedRole === 'all' || role === selectedRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
    <script>
        function openEditModal(userId, email, role, status) {
            document.getElementById('editUserId').value = userId;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
            document.getElementById('editStatus').value = status;
            
            const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editModal.show();
        }
    </script>
</body>
</html>
