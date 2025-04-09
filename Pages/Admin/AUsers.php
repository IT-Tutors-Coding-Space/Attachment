<?php
require_once "../../db.php";
session_start();

if ($_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/ALogin.php");
    exit();
}

require "../../Components/AdminNav.php";

// Get all users with proper names based on role
$users = $conn->query("
    SELECT 
        u.user_id,
        u.email,
        u.role,
        u.status,
        CASE 
            WHEN u.role = 'student' THEN s.full_name
            WHEN u.role = 'company' THEN c.company_name
            WHEN u.role = 'admin' THEN a.full_name
            ELSE NULL
        END as name
    FROM users u
    LEFT JOIN students s ON u.user_id = s.student_id AND u.role = 'student'
    LEFT JOIN companies c ON u.user_id = c.company_id AND u.role = 'company'
    LEFT JOIN admins a ON u.user_id = a.admin_id AND u.role = 'admin'
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4e73df;
            --success: #1cc88a;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
        }
        
        .user-card {
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .user-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        
        .role-badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
        }
        
        .status-badge {
            width: 80px;
            text-align: center;
        }
        
        .search-box {
            border-radius: 20px;
            padding-left: 40px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%239C9C9C' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 15px center;
        }
    </style>
</head>
<body class="bg-light">
    <?php require "../../Components/AdminNav.php"; ?>

    <div class="container py-4"><br><br><br><br>
        <!-- Header with Search and Add User -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div class="mb-3 mb-md-0">
                <h1 class="h3 mb-1 text-gray-800">User Management</h1>
                <p class="mb-0 text-muted">Manage all system users</p>
            </div>
            <div class="d-flex flex-column flex-md-row gap-3 w-100 w-md-auto">
                <div class="position-relative flex-grow-1">
                    <input type="text" class="form-control search-box" id="searchUsers" 
                           placeholder="Search users..." onkeyup="searchUsers()">
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-1"></i> Add User
                </button>
            </div>
        </div>

        <!-- Role Filter Tabs -->
        <ul class="nav nav-tabs mb-4" id="roleTabs">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-role="all">All Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-role="student">Students</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-role="company">Companies</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-role="admin">Admins</a>
            </li>
        </ul>

        <!-- Users Table -->
        <div class="card user-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <?php foreach ($users as $user): ?>
                            <tr data-role="<?= $user['role'] ?>">
                                <td><?= $user['user_id'] ?></td>
                                <td><?= $user['name'] !== null ? htmlspecialchars($user['name']) : 'N/A' ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge role-badge bg-<?= 
                                        $user['role'] === 'admin' ? 'danger' : 
                                        ($user['role'] === 'company' ? 'info' : 'warning') 
                                    ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge status-badge bg-<?= 
                                        $user['status'] == 'Active' ? 'success' : 'secondary' 
                                    ?>">
                                        <?= $user['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary" 
                                            onclick="openEditModal(
                                                '<?= $user['user_id'] ?>',
                                                '<?= htmlspecialchars($user['email']) ?>',
                                                '<?= $user['role'] ?>',
                                                '<?= $user['status'] ?>'
                                            )">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="deleteUser.php" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php require "../../Components/AdminFooter.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        // Tab filtering
        document.querySelectorAll('#roleTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const role = this.getAttribute('data-role');
                filterUsers(role);
                
                // Update active tab
                document.querySelectorAll('#roleTabs .nav-link').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        function filterUsers(role = 'all') {
            const rows = document.querySelectorAll('#userTableBody tr');
            rows.forEach(row => {
                if (role === 'all' || row.getAttribute('data-role') === role) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function searchUsers() {
            const input = document.getElementById('searchUsers').value.toLowerCase();
            const rows = document.querySelectorAll('#userTableBody tr');
            rows.forEach(row => {
                if (row.style.display === 'none') return;
                
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                row.style.display = (name.includes(input) || email.includes(input)) ? '' : 'none';
            });
        }

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
