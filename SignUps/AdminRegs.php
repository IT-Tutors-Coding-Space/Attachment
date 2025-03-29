<?php
session_start();
require_once "../db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input fields
    if (empty($_POST["full_name"]) || empty($_POST["email"]) || empty($_POST["password"]) || empty($_POST["confirm_password"])) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    // Validate email format
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email format."]);
        exit();
    }

    // Validate password confirmation
    if ($_POST["password"] !== $_POST["confirm_password"]) {
        echo json_encode(["success" => false, "message" => "Passwords do not match."]);
        exit();
    }

    $email = $_POST["email"];
    $adminName = $_POST["full_name"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT); // Secure hashing

    // Check for duplicate email in `users` table
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(["success" => false, "message" => "Email is already registered."]);
        exit();
    }

    try {
        $conn->beginTransaction();

        // Insert into `users` table first
        $stmt = $conn->prepare("INSERT INTO users (email, password_hash, role, created_at) VALUES (?, ?, 'admin', NOW())");
        $stmt->execute([$email, $password]);
        $user_id = $conn->lastInsertId(); // Get the inserted user ID

        // Insert into `admins` table
        $stmt = $conn->prepare("INSERT INTO admins (user_id, full_name, email, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $adminName, $email, $password]);

        $_SESSION["user_id"] = $user_id;
        $_SESSION["role"] = "admin";

        $conn->commit();

        echo json_encode(["success" => true, "message" => "Registration successful! Redirecting..."]);
        header(header: "Location: Alogin.php");

        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up - AttachME</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/signup.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <!-- <h2 style="margin-left: 40%;" class="text-white fw-bold fs-3">👨‍💼 AttachME - Admin Sign Up</h2> -->
            <!-- <a href="index.html" class="btn btn-outline-light">🏠 Home</a> -->
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1 d-flex justify-content-center align-items-center">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg w-100" style="max-width: 500px;">
            <h5 class="fw-bold text-center text-primary mb-3">Create Your Admin Account</h5>
            
            <!-- Admin Signup -->
            <form id="adminSignupForm" class="signup-form" method="POST" action="../SignUps/AdminRegs.php">
                <h6 style="text-align: center;" class="fw-bold text-secondary">👨‍💼 Admin Registration</h6>
                <div class="mb-3">
                    <label for="adminName" class="form-label">Admin Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input name="full_name" type="text" class="form-control" id="adminName" placeholder="Enter admin name" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="adminEmail" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                        <input name="email" type="email" class="form-control" id="adminEmail" placeholder="Enter email" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="adminPassword" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input name="password" type="password" class="form-control" id="adminPassword" placeholder="Create a strong password" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="confirmAdminPassword" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input name="confirm_password" type="password" class="form-control" id="confirmAdminPassword" placeholder="Re-enter password" required>
                    </div>
                </div>
                <button name="submit" type="submit" class="btn btn-primary w-100">Sign Up as Admin</button>
            </form>
            
            <p class="text-center mt-3">Already have an account? <a href="login.html" class="text-primary fw-bold">Log In</a></p>
        </div>
    </div>
<script src="../Javasript/AdminReg.js"></script>
    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="help-center.html" class="text-white fw-bold">Help Center</a>
            <a href="terms.html" class="text-white fw-bold">Terms of Service</a>
            <a href="contact.html" class="text-white fw-bold">Contact Support: 0700234362</a>
        </div>
    </footer>
</body>
</html>











