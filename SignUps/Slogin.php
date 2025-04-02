<?php
require_once "../db.php";
session_start();

$error = ""; // Initialize error variable

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate email
    if (empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (!isset($_POST["password"]) || empty($_POST["password"])) {
        $error = "Password field is required.";
    } else {
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);

        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["password"])) {
                // Debugging: Log the successful verification
                error_log("Login successful for email: " . $email);

                // Debugging: Log the successful verification
                error_log("Login successful for email: " . $email);


                $_SESSION["user_id"] = $user["student_id"];
                $_SESSION["role"] = "student";

                header("Location: ../Pages/Students/SDashboard.php"); // Redirect to AHome.php after successful login
                exit();
            } else {
                $error = "Invalid email or password. Please check your credentials.";
                // Debugging: Log the failed login attempt
                error_log("Failed login attempt for email: " . $email);

                // Debugging: Log the failed login attempt
                error_log("Failed login attempt for email: " . $email);

            }
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            $error = "A server error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AttachME</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3" style="margin-left: 45%;">🔐 AttachME - Login</h2>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1 d-flex justify-content-center align-items-center">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg w-100" style="max-width: 400px;">
            <h5 class="fw-bold text-center text-primary mb-3">Log In to Your Account</h5>
            
            <!-- Display Error Message -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="loginForm" method="POST">

                <h6 class="fw-bold text-secondary text-center">👨Student Login</h6>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                        <input name="email" type="email" class="form-control" id="email" placeholder="Enter your email" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input name="password" type="password" class="form-control" id="password" placeholder="Enter your password" required>
                        <span class="input-group-text toggle-password"><i class="fa fa-eye"></i></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            
            <p class="text-center mt-3">
                <a href="forgot-password.html" class="text-primary fw-bold">Forgot Password?</a>
            </p>
        </div>
    </div>
    <script src="../js/interact.js"></script>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="help-center.html" class="text-white fw-bold">Help Center</a>
            <a href="terms.html" class="text-white fw-bold">Terms of Service</a>
            <a href="contact.html" class="text-white fw-bold">Contact Support:</a>
        </div>
    </footer>
    
</body>
</html>
