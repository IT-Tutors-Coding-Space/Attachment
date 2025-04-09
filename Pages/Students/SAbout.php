<?php
require_once "../../db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location:  ../SignUps/SLogin.php");
    exit();
}

$student_id = $_SESSION["user_id"];
require "../../Components/StudentNav.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About AttachME - Student Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../CSS/styles.css">
</head>
<body>
    <main class="container p-5 flex-grow-1">
        <header class="text-center mb-4">
            <h1 class="text-3xl fw-bold">Welcome to AttachME</h1>
            <p class="text-muted">Empowering students through seamless attachment opportunities.</p>
        </header>

        <!-- About Section -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
            <h5 class="fw-bold text-primary">Our Mission</h5>
            <p>We're changing the game when it comes to student internships at AttachME. Our goal is simple: to connect you with companies so you can gain the real-world skills and experiences you need to shine in your career..</p>
            
            <h5 class="fw-bold text-primary">Our Vision</h5>
            <p>We want to be the leading platform where students can easily find real, trustworthy internship opportunities that help them grow and connect with the professional world.</p>
            
            <h5 class="fw-bold text-primary">Core Values</h5>
            <ul>
                <li> <strong>Innovation:</strong> Continuously improving our platform to meet industry demands.</li>
                <li> <strong>Integrity:</strong> Ensuring transparency and fairness in all applications.</li>
                <!-- <li> <strong>Empowerment:</strong> Providing students with the resources to excel.</li> -->
                <li> <strong>Accessibility:</strong> Making opportunities available to all students.</li>
            </ul>
        </div>

        <!-- How It Works Section -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg mt-4">
            <h5 class="fw-bold text-primary">How It Works</h5>
            <ul>
                <li> <strong>For Students:</strong> Sign up, browse verified attachment opportunities, apply, and track progress in real-time.</li>
                <li> <strong>For Companies:</strong> Post attachment openings, receive applications, and recruit the best candidates.</li>
                <li> <strong>For Admins:</strong> Oversee platform operations.</li>
            </ul>
        </div>

        <!-- Why Choose AttachME -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg mt-4">
            <h5 class="fw-bold text-primary">Why Choose AttachME?</h5>
            <ul>
                <li>✅ User-friendly and intuitive platform for easy navigation.</li>
                <li>✅ Verified companies and opportunities ensure reliability.</li>
                <li>✅ Secure application process with real-time updates.</li>
                <li>✅ Dedicated support system for both students and recruiters.</li>
            </ul>
            <p>As students, we are continuously learning and growing as we build this application. Our enthusiasm and commitment drive us to overcome challenges and achieve our goals.</p>
        </div>
     

        </div>

        <!-- Team Section -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg mt-4 text-center">
            <h5 class="fw-bold text-primary">Meet Our Team</h5>
            <p>Behind AttachME is a team with passion dedicated to student career growth.</p>
            <div class="row g-4 d-flex justify-content-center">
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm p-3 bg-light rounded-lg">
                        <img src="../../Images/logo.png" class="rounded-circle mx-auto d-block" width="100" height="100" alt="Hedmon Achacha">
                        <h6 class="fw-bold mt-2">Hedmon Achacha</h6>
                        <!-- <p class="text-muted">Project Lead</p> -->
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm p-3 bg-light rounded-lg">
                        <img src="images/vincent.jpg" class="rounded-circle mx-auto d-block" width="100" height="100" alt="Vincent Owuor">
                        <h6 class="fw-bold mt-2">Vincent Owuor</h6>
                        <!-- <p class="text-muted">Backend Developer</p> -->
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm p-3 bg-light rounded-lg">
                        <img src="images/richard.jpg" class="rounded-circle mx-auto d-block" width="100" height="100" alt="Richard Ochieng">
                        <h6 class="fw-bold mt-2">Richard Ochieng</h6>
                        <!-- <p class="text-muted">Frontend Developer</p> -->
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm p-3 bg-light rounded-lg">
                        <img src="images/vera.jpg" class="rounded-circle mx-auto d-block" width="100" height="100" alt="Vera Brenda">
                        <h6 class="fw-bold mt-2">Vera Brenda</h6>
                        <!-- <p class="text-muted">UI/UX Designer</p> -->
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm p-3 bg-light rounded-lg">
                        <img src="images/pheniuis.jpg" class="rounded-circle mx-auto d-block" width="100" height="100" alt="Pheniuis Mutiga">
                        <h6 class="fw-bold mt-2">Phenius Mutiga</h6>
                        <!-- <p class="text-muted">System Analyst</p> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../Help Center.php" class="text-white fw-bold">Help Center</a>
            <a href="../Students/Terms of service.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../Students/Contact Support.php" class="text-white fw-bold">Contact Support:</a>
        </div>
    </footer>
=======
    </main>
    <?php require "../../Components/StudentFooter.php"; ?>
>>>>>>> d7a7306aa262dea58932b91eb35201da20f5463f
</body>
</html>
