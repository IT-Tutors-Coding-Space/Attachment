<?php
require_once('../../db.php');

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../SignUps/Slogin.php");
    exit();
}
$student_id = $_SESSION['user_id'];
require "../../Components/StudentNav.php";
try {
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/student-styles.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100"><br><br><b></b>
    
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h1 class="text-3xl fw-bold">My Profile</h1>
        </header>

        <!-- Profile Section -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
            <div class="row">
               

                <!-- Profile Information Column -->
                <div class="col-md-8"><b></b><br>
                    <!-- Personal Information Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title fw-bold mb-4 d-flex align-items-center">
                                <i class="bi bi-person-circle me-2"></i>Personal Information
                            </h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-muted">Full Name</label>
                                    <div class="form-control bg-light"><?php echo htmlspecialchars($profile['full_name']); ?></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-muted">Email</label>
                                    <div class="form-control bg-light"><?php echo htmlspecialchars($profile['email']); ?></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-muted">Course</label>
                                    <div class="form-control bg-light"><?php echo $profile['course']; ?></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-muted">Level</label>
                                    <div class="form-control bg-light"><?php echo $profile['level']; ?></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-muted">Year of Study</label>
                                    <div class="form-control bg-light"><?php echo $profile['year_of_study']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings Card -->
                    <div class="card mb-4">
                       
                            <div class="card-body">
                                <h5 class="card-title fw-bold d-flex align-items-center">
                                    <i class="bi bi-shield-lock me-2"></i>Password Settings
                                </h5>
                                <form id="passwordForm" class="needs-validation" novalidate>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" class="form-control" name="current_password" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">New Password</label>
                                            <input type="password" class="form-control" name="new_password" required minlength="8">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" name="confirm_password" required>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check-circle me-1"></i>Update Password
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Account Actions Card -->
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="fw-bold mb-0 d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Danger Zone
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="fw-bold">Delete Account</h6>
                                        <p class="small text-muted mb-0">
                                            This will permanently remove all your data including applications and profile.
                                            This action cannot be undone.
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <button class="btn btn-outline-danger" id="deleteAccountBtn">
                                            <i class="bi bi-trash-fill me-1"></i>Delete Account
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
                                        <a href="../../SignUps/Slogin.php" class="btn btn-outline-secondary">
                                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // Enhanced Password Form Handling
    document.getElementById('passwordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        // Client-side validation
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        if (form.new_password.value !== form.confirm_password.value) {
            alert('New passwords do not match!');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';

        try {
            const formData = new FormData(form);
            const response = await fetch('../../api/update-password.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Password updated successfully!');
                form.reset();
                form.classList.remove('was-validated');
            } else {
                throw new Error(data.error || 'Password update failed');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });

    // Account Deletion with Confirmation
    document.getElementById('deleteAccountBtn').addEventListener('click', async function() {
        if (!confirm('WARNING: This will permanently delete your account and all data.\n\nAre you absolutely sure?')) {
            return;
        }
        
        const confirmation = prompt('Type "DELETE" to confirm account deletion:');
        if (confirmation !== 'DELETE') {
            alert('Account deletion cancelled');
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';

        try {
            const response = await fetch('../../api/delete-account.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    student_id: '<?php echo $student_id; ?>',
                    confirmation: confirmation
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Account deleted successfully. Redirecting to login...');
                window.location.href = '../../SignUps/Slogin.php';
            } else {
                throw new Error(data.error || 'Account deletion failed');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Ensure logout redirects properly
    document.getElementById('logoutBtn').addEventListener('click', function() {
        window.location.href = '../../auth/logout.php';
    });
    </script>

    <?php require "../../Components/StudentFooter.php"; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javasript/SProfile.js"></script>
</body>
</html>
