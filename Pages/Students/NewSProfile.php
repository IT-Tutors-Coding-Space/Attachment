<?php
session_start();
require "../../db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../../SignUps/Slogin.php");
    exit();
}

$student_id = $_SESSION["user_id"];
$error = $success = '';

// Get current student data
try {
    $stmt = $conn->prepare("SELECT full_name, profile_picture FROM students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching profile: " . $e->getMessage();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["update_profile"])) {
        // Update name and profile picture
        $full_name = $_POST["full_name"] ?? '';
        
        try {
            // Handle file upload
            $profile_picture = $student["profile_picture"];
            if (!empty($_FILES["profile_picture"]["name"])) {
                $target_dir = "../../Images/profiles/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $file_ext = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
                $new_filename = "student_" . $student_id . "_" . time() . "." . $file_ext;
                $target_file = $target_dir . $new_filename;
                
                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                    $profile_picture = "profiles/" . $new_filename;
                }
            }
            
            $stmt = $conn->prepare("UPDATE students SET full_name = ?, profile_picture = ? WHERE student_id = ?");
            $stmt->execute([$full_name, $profile_picture, $student_id]);
            $success = "Profile updated successfully!";
            header("Refresh:0");
        } catch (PDOException $e) {
            $error = "Error updating profile: " . $e->getMessage();
        }
    } 
    elseif (isset($_POST["change_password"])) {
        // Change password
        $current_password = $_POST["current_password"] ?? '';
        $new_password = $_POST["new_password"] ?? '';
        $confirm_password = $_POST["confirm_password"] ?? '';
        
        try {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM students WHERE student_id = ?");
            $stmt->execute([$student_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!password_verify($current_password, $result["password"])) {
                $error = "Current password is incorrect";
            } elseif ($new_password !== $confirm_password) {
                $error = "New passwords don't match";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE students SET password = ? WHERE student_id = ?");
                $stmt->execute([$hashed_password, $student_id]);
                $success = "Password changed successfully!";
            }
        } catch (PDOException $e) {
            $error = "Error changing password: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../CSS/SProfile.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="../../Images/<?= htmlspecialchars($student['profile_picture'] ?? 'default_profile.png') ?>" 
                             class="rounded-circle mb-3" width="150" height="150">
                        <h4><?= htmlspecialchars($student['full_name'] ?? '') ?></h4>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Update Profile</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" 
                                       value="<?= htmlspecialchars($student['full_name'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" name="profile_picture" accept="image/*">
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5>Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../Javasript/SProfile.js"></script>
</body>
</html>
