<?php
require_once "../db.php";
session_start();

$project_name = "AttachME";
$error = "";
$success = "";

// Validate token and process password reset
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Verify token exists and isn't expired
    $stmt = $conn->prepare("SELECT * FROM password_reset_tokens WHERE token = ? AND expiration > NOW() AND used = 0");
    $stmt->execute([password_hash($token, PASSWORD_DEFAULT)]);
    $token_record = $stmt->fetch();
    
    if (!$token_record) {
        $error = "Invalid or expired password reset link";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $token = $_POST['token'];
    
    // Validate passwords
    if (empty($new_password) || strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Validate token
        $stmt = $conn->prepare("SELECT * FROM password_reset_tokens WHERE token = ? AND expiration > NOW() AND used = 0");
        $stmt->execute([password_hash($token, PASSWORD_DEFAULT)]);
        $token_record = $stmt->fetch();
        
        if ($token_record) {
            $email = $token_record['email'];
            
            // Check which table the email belongs to
            $stmt = $conn->prepare("SELECT 'users' as table_name FROM users WHERE email = ? UNION SELECT 'admins' as table_name FROM admins WHERE email = ? UNION SELECT 'companies' as table_name FROM companies WHERE email = ?  UNION SELECT 'students' as table_name FROM students WHERE email = ?");
            $stmt->execute([$email, $email, $email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Update password in the appropriate table
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE {$user['table_name']} SET password = ? WHERE email = ?");
                $stmt->execute([$hashed_password, $email]);
                
                // Mark token as used
                $stmt = $conn->prepare("UPDATE password_reset_tokens SET used = 1 WHERE token_id = ?");
                $stmt->execute([$token_record['token_id']]);
                
                $success = "Password has been reset successfully";
            } else {
                $error = "Invalid token or user not found";
            }
        } else {
            $error = "Invalid or expired token";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - <?php echo $project_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/Login.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3" style="margin-left: 45%;">🔐 <?php echo $project_name; ?> - Password Reset</h2>
        </div>
    </nav>
    
    <div class="container p-5 flex-grow-1 d-flex justify-content-center align-items-center">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg w-100" style="max-width: 400px;">
            <h5 class="fw-bold text-center text-primary mb-3">Set New Password</h5>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['token']) && empty($error)): ?>
                <form method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            <input name="new_password" type="password" class="form-control" id="new_password" placeholder="Enter new password" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            <input name="confirm_password" type="password" class="form-control" id="confirm_password" placeholder="Confirm new password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Update Password</button>
                </form>
            <?php elseif (empty($success)): ?>
                <div class="alert alert-warning text-center">
                    Invalid or expired password reset link. Please request a new one.
                </div>
                <a href="forgot_password.php" class="btn btn-secondary w-100">Request New Reset Link</a>
            <?php endif; ?>
            
            <p class="text-center mt-3">
                <a href="Slogin.php" class="text-primary fw-bold">Back to Login</a>
            </p>
        </div>
    </div>
    
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 <?php echo $project_name; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
