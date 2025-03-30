<?php
session_start();
require_once "../db.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input fields
    if (empty($_POST["company_name"]) || empty($_POST["email"]) || empty($_POST["location"]) || empty($_POST["industry"]) || empty($_POST["password"]) || empty($_POST["confirm_password"])) {
        error_log("Registration error: All fields are required.");
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    // Validate email format
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $_POST["email"])) {
        echo json_encode(["success" => false, "message" => "Invalid email format"]);
        exit();
    }

    // Validate password confirmation
    if ($_POST["password"] !== $_POST["confirm_password"]) {
        echo "Passwords do not match.";
        exit();
    }

    // Check for duplicate email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(["success" => false, "message" => "Email is already registered."]);
        exit();
    }

    $fullName = $_POST["company_name"];
    $email = $_POST["email"];
    $location = $_POST["location"];
    $industry = $_POST["industry"];
    $password = $_POST["password"]; // Hash the password before storing
    $hashedpassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password before storing

    try {
        $conn->beginTransaction();
        //insert into companies

        $stmt = $conn->prepare("INSERT INTO companies (company_name, email, location, industry, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$fullName, $email, $location, $industry, $hashedpassword]);
        $company_id = $conn->lastInsertId(); // Get the newly inserted company_id

        // Include the user_id in the users table
        $stmt = $conn->prepare("INSERT INTO users (company_id, email, password, role, created_at) VALUES (?, ?, ?, 'company', NOW())");
        $stmt->execute([$company_id, $email, $hashedpassword]);
        $user_id = $conn->lastInsertId(); // Get the newly inserted user_id

        // Update the companies table with the user_id
        $stmt = $conn->prepare("UPDATE companies SET user_id = ? WHERE company_id = ?");
        $stmt->execute([$user_id, $company_id]);

        $_SESSION["user_id"] = $user_id; // Set session user_id to the newly created user_id
        $_SESSION["role"] = "company"; // Set the role to "company"

        $conn->commit();

        // Redirect to loginn.php after successful registration
        echo json_encode(["success" => true, "message" => "Registration successful!"]);
        header("Location: Clogin.php");
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
    <title>Company Sign Up - AttachME</title>
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
            <!-- <h2 style="margin-left: 40%;" class="text-white fw-bold fs-3"> AttachME - Company Sign Up</h2> -->
            <!-- <a href="index.html" class="btn btn-outline-light"> Home</a> -->
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1 d-flex justify-content-center align-items-center">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg w-100" style="max-width: 500px;">
            <h5 class="fw-bold text-center text-primary mb-3">Create Your Company Account</h5>
            
            <!-- Company Signup -->
            <form id="companySignupForm" class="signup-form" method="POST" action="../SignUps/CompanyReg.php">
                <h6 style="text-align: center;" class="fw-bold text-secondary">🏢 Company Registration</h6>
                <div class="mb-3">
                    <label for="companyName" class="form-label">Company Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-building"></i></span>
                        <input name="company_name" type="text" class="form-control" id="companyName" placeholder="Enter company name" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="companyEmail" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                        <input name="email" type="email" class="form-control" id="companyEmail" pattern="^[a-zA-Z0-9._%+-]+@attachme\.company$" placeholder="username@gmail.com" quired>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="companyLocation" class="form-label">Company Location</label>
                    <select name="location" class="form-control" id="companyLocation" required>
                        <option value="">Select County</option>
                        <option value="Nairobi">Nairobi</option>
                        <option value="Mombasa">Mombasa</option>
                        <option value="Kisumu">Kisumu</option>
                        <option value="Nakuru">Nakuru</option>
                        <option value="Uasin Gishu">Uasin Gishu</option>
                        <option value="Kiambu">Kiambu</option>
                        <option value="Machakos">Machakos</option>
                        <option value="Kajiado">Kajiado</option>
                        <option value="Bungoma">Bungoma</option>
                        <option value="Kakamega">Kakamega</option>
                        <option value="Meru">Meru</option>
                        <option value="Nyeri">Nyeri</option>
                        <option value="Embu">Embu</option>
                        <option value="Kisii">Kisii</option>
                        <option value="Migori">Migori</option>
                        <option value="Busia">Busia</option>
                        <option value="Siaya">Siaya</option>
                        <option value="Homa Bay">Homa Bay</option>
                        <option value="Baringo">Baringo</option>
                        <option value="Trans Nzoia">Trans Nzoia</option>
                        <option value="Vihiga">Vihiga</option>
                        <option value="Turkana">Turkana</option>
                        <option value="Garissa">Garissa</option>
                        <option value="Taita Taveta">Taita Taveta</option>
                        <option value="Narok">Narok</option>
                        <option value="Kericho">Kericho</option>
                        <option value="Bomet">Bomet</option>
                        <option value="Tharaka Nithi">Tharaka Nithi</option>
                        <option value="Mandera">Mandera</option>
                        <option value="Marsabit">Marsabit</option>
                        <option value="West Pokot">West Pokot</option>
                        <option value="Samburu">Samburu</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="companyIndustry" class="form-label">Industry</label>
                    <select name="industry" class="form-control" id="companyIndustry" required>
                        <option value="">Select Industry</option>
                        <option value="Technology">Technology</option>
                        <option value="Finance">Finance</option>
                        <option value="Healthcare">Healthcare</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="companyPassword" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input name="password" type="password" class="form-control" id="companyPassword" placeholder="Create a strong password" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="confirmCompanyPassword" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input name="confirm_password" type="password" class="form-control" id="confirmCompanyPassword" placeholder="Re-enter password" required>
                    </div>
                </div>
                <button name="submit" type="submit" class="btn btn-primary w-100">Sign Up as Company</button>
            </form>
            
            <p class="text-center mt-3">Already have an account? <a href="login.html" class="text-primary fw-bold">Log In</a></p>
        </div>
    </div>
<script src="../Javasript/CompanyReg.js"></script>
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
