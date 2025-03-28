<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - AttachME</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/signup.css">
</head>
<body class="bg-gray 100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 style="text-align: center; margin-left: 40%;" class="text-white fw-bold fs-3">📝 AttachME - Sign Up</h2>
            <!-- <a href="index.html" class="btn btn-outline-light">🏠 Home</a> -->
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1 d-flex justify-content-center align-items-center">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg w-100" style="max-width: 500px;">
            <h5 style="text-align: center;" class="fw-bold text-center text-primary mb-3">Create Your Student Account</h5>
            
            <!-- Student Signup -->
            <form name="form" id="studentSignupForm" class="signup-form" action="../SignUps/StudentReg.php" method="POST">
                <h6 style="text-align: center;" class="fw-bold text-secondary">🎓 Student Registration</h6>
                <div class="mb-3">
                    <label for="studentName" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" id="studentName" placeholder="Enter your full name" name="full_name" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="studentEmail" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                        <input type="email" class="form-control" id="studentEmail" pattern="^[a-zA-Z0-9._%+-]+@attachme\.student$" placeholder="username@attachme.student" name="email" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="studentGender" class="form-label">Gender</label>
                    <select name="gender" class="form-control" id="studentGender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="studentRegNo" class="form-label">Registration Number</label>
                    <input type="text" name="registration_number" class="form-control" id="studentRegNo" placeholder="Enter your registration number" required>
                </div>
                <div class="mb-3">
                    <label for="studentLevel" class="form-label">Level of Study</label>
                    <select name="level" class="form-control" id="studentLevel" required>
                        <option value="">Select Level</option>
                        <option value="Certificate">Certificate</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Degree">Degree</option>
                        <option value="Masters">Masters</option>
                    </select>
                </div>
                
                    <div class="mb-3">
                    <label for="studentYear" class="form-label">Year of Study</label>
                    <select name="year_of_study" class="form-control" id="studentYear" required>
                        <option value="">Select Year</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="studentCourse" class="form-label">Course</label>
                    <select name="course" class="form-control" id="studentCourse" required>
                        <option value="">Select Course</option>
                        <option value="IT">Information Technology</option>
                        <option value="CS">Computer Science</option>
                        <option value="SIK">SIK</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="studentPassword" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input name="password" type="password" class="form-control" id="studentPassword" placeholder="Create a strong password" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input name="password" type="password" class="form-control" id="confirmPassword" placeholder="Re-enter password" required>
                    </div>
                </div>
                <button  type="submit" class="btn btn-primary w-100">Sign Up as Student</button>
            </form>
            
            <p class="text-center mt-3">Already have an account? <a href="../SignUps/Loginn.php" class="text-primary fw-bold">Log In</a></p>
        </div>
    </div>
    <script src="../Javasript/StudentReg.js"></script>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="help-center.html" class="text-white fw-bold">Help Center</a>
            <a href="terms.html" class="text-white fw-bold">Terms of Service</a>
            <a href="contact.html" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>
</body>
</html>
