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
            <h2 style="margin-left: 45%;" class="text-white fw-bold fs-3">🔐 AttachME - Login</h2>
            <!-- <a href="index.html" class="btn btn-outline-light">🏠 Home</a> -->
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1 d-flex justify-content-center align-items-center">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg w-100" style="max-width: 400px;">
            <h5 class="fw-bold text-center text-primary mb-3">Log In to Your Account</h5>
            
            <!-- Login Form -->
            <form id="loginForm" class="login-form">
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
                        <input name="email" type="password" class="form-control" id="password" placeholder="Enter your password" required>
                        <span class="input-group-text toggle-password"><i class="fa fa-eye"></i></span>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Select Role</label>
                    <select name="role" class="form-control" id="role" required>
                        <option value="">Choose your role</option>
                        <option value="admin">Admin</option>
                        <option value="company">Company</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            
            <p class="text-center mt-3">
                <a href="forgot-password.html" class="text-primary fw-bold">Forgot Password?</a>
            </p>
            <!-- <p class="text-center mt-2">
                Don't have an account? <a href="student-signup.html" class="text-primary fw-bold">Sign Up</a>
            </p> -->
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="help-center.html" class="text-white fw-bold">Help Center</a>
            <a href="terms.html" class="text-white fw-bold">Terms of Service</a>
            <a href="contact.html" class="text-white fw-bold">Contact Support:</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="js/login.js"></script>
    <!-- <script>
        document.getElementById("loginForm").addEventListener("submit", function (event) {
            event.preventDefault();

            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            fetch("../?Login.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Login successful! Redirecting...");
                        window.location.href = ""; // Redirect to dashboard
                    } else {
                        alert("Login failed: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An error occurred. Please try again.");
                });
        });
    </script> -->
</body>
</html>
