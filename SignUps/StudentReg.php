<?php
// Start output buffering to prevent stray output
ob_start();
session_start();
require_once "../db.php";
<<<<<<< HEAD

// Remove any accidental SMTP configuration output
if (ob_get_contents()) {
    ob_clean();
}
=======
>>>>>>> 6f3d3a023608c00074b8df5b85ab0c12241a24bd

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input fields
    if (empty($_POST["full_name"]) || empty($_POST["email"]) || empty($_POST["level"]) || empty($_POST["year_of_study"]) || empty($_POST["course"]) || empty($_POST["password"]) || empty($_POST["confirm_password"])) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    // Validate name format
    if (!preg_match('/^[a-zA-Z][a-zA-Z \'-]{1,48}[a-zA-Z]$/', $_POST["full_name"])) {
        echo json_encode(["success" => false, "message" => "Invalid name format. Only letters, spaces, hyphens or apostrophes allowed (3-50 characters)."]);
        exit();
    }

    // Validate email format
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $_POST["email"])) {
        echo json_encode(["success" => false, "message" => "Invalid email format. Only @gmail.com is allowed."]);
        exit();
    }

    // Validate password confirmation
    if ($_POST["password"] !== $_POST["confirm_password"]) {
        echo "Passwords do not match.";
        exit();
    }

    // Check for duplicate email
    $email = $_POST["email"];
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(["success" => false, "message" => "Email is already registered."]);
        exit();
    }

    $fullName = $_POST["full_name"];
    $email = $_POST["email"];
    $level = $_POST["level"];
    $yearOfStudy = $_POST["year_of_study"];
    $course = $_POST["course"];
    $password= $_POST["password"];
    $hashedpassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password before storing

    try {
        $conn->beginTransaction();

    $stmt = $conn->prepare("INSERT INTO students (full_name, email, level, password, year_of_study, course, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute([$fullName, $email, $level, $hashedpassword, $yearOfStudy, $course]);

    $student_id = $conn->lastInsertId(); // Get the newly inserted student_id

    //insert into users table
    $stmt = $conn->prepare("INSERT INTO users (student_id, email, password, role, created_at) VALUES (?, ?, ?, 'student', NOW())");
    $stmt->execute([$student_id, $email, $hashedpassword]);
    $student_id = $conn->lastInsertId(); // Get the newly inserted student_id
    $user_id = $conn->lastInsertId(); // Get the inserted user ID
        // Update the students table with the user_id
        $stmt = $conn->prepare("UPDATE students SET user_id = ? WHERE student_id = ?");
        $stmt->execute([$user_id, $student_id]);

        $_SESSION["user_id"] = $user_id; // Set session user_id to the newly created user_id
        $_SESSION["role"] = "student";

        $conn->commit();

        // Redirect to login page after successful registration
        echo json_encode(["success" => true, "message" => "Registration successful!"]);
        header(header: "Location: Slogin.php");

        exit();
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Sign Up - AttachME</title>
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
            <!-- <h2 style="margin-left: 40%;" class="text-white fw-bold fs-3">🎓 AttachME - Student Sign Up</h2> -->
            <!-- <a href="index.html" class="btn btn-outline-light">Home</a> -->
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1 d-flex justify-content-center align-items-center">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg w-100" style="max-width: 500px;">
            <h5 class="fw-bold text-center text-primary mb-3">Create Your Student Account</h5>
            
            <!-- Student Signup -->
            <form id="studentSignupForm" class="signup-form" method="POST" action="../SignUps/StudentReg.php">
                <h6 style="text-align: center;" class="fw-bold text-secondary">🎓 Student Registration</h6>
                <div class="mb-3">
                    <label for="fullName" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input name="full_name" type="text" class="form-control" id="fullName" placeholder="Enter full name" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="studentEmail" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                        <input name="email" type="email" class="form-control" id="studentEmail" placeholder="Enter email" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="studentLevel" class="form-label">Level</label>
                    <select name="level" class="form-control" id="studentLevel" required>
                        <option value="">Select Level</option>
                        <option value="Certificate">Certificate</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Diploma">Degree</option>
                        <option value="Diploma">Masters</option>


                    </select>
                </div>
                <div class="mb-3">
                    <label for="course" class="form-label">Course</label>
                    <input name="course" type="text" class="form-control" id="course" placeholder="Enter course" required>
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
                    <label for="studentPassword" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input name="password" type="password" class="form-control" id="studentPassword" placeholder="Create a strong password" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="confirmStudentPassword" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input name="confirm_password" type="password" class="form-control" id="confirmStudentPassword" placeholder="Re-enter password" required>
                    </div>
                </div>
                <button name="submit" type="submit" class="btn btn-primary w-100">Sign Up as Student</button>
            </form>
            
            <p class="text-center mt-3">
                Already registered? <a href="Slogin.php" class="text-primary fw-bold">Login here</a>
            </p>
        </div>
    </div>
<script src="../Javasript/StudentReg.js"></script>
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
