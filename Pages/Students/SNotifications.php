<?php
require_once '../../db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') { 
    header("Location: ../SignUps/Slogin.php");
    exit(); 
}
$student_id = $_SESSION['user_id'];
try {
    $stmt = $conn->prepare("SELECT * FROM messages WHERE sender_id = ? ORDER BY sent_at DESC");
    $stmt->execute([$student_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - AttachME</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/student.css">
</head>

<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME - Student Portal</h2>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="../Students/SDashboard.php" class="nav-link text-white fw-bold fs-5 active">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="../Students/SAbout.php" class="nav-link text-white fw-bold fs-5 active">📖 About Us</a></li>

                <li class="nav-item"><a href="../Students/SBrowse.php" class="nav-link text-white fw-bold fs-5">🔍 Browse Opportunities</a></li>
                <li class="nav-item"><a href="../Students/SApplicationSubmission.php" class="nav-link text-white fw-bold fs-5">📄 My Applications</a></li>
                <li class="nav-item"><a href="../Students/SNotifications.php" class="nav-link text-white fw-bold fs-5">💬 Messages</a></li>
                <li class="nav-item"><a href="../Students/SProfile.php" class="nav-link text-white fw-bold fs-5">👤 Profile</a></li>
                <li class="nav-item"><a href="../Students/SSettings.php" class="nav-link text-white fw-bold fs-5">⚙️ Settings</a></li>
            </ul>
        </div>
    </nav>
    
    <h1>Notifications</h1>
    <ul>
        <?php foreach ($messages as $message): ?>
            <li><?php echo $message['message']; ?></li>
        </li>
        <?php endforeach; ?>
    </ul>
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <h4 class="fw-bold text-primary">📩 Messages</h4>
        <p class="text-muted">View and respond to messages from companies.</p>
        
        <div class="card shadow p-3 mb-4">
            <div class="d-flex align-items-center border-bottom pb-2">
                <img src="../../Images/logo.png" alt="Profile Picture" class="rounded-circle" width="40" height="40">
                <h6 class="ms-2">Company HR - AttachME</h6>
            </div>
            <div class="chat-box" id="messageList" style="max-height: 300px; overflow-y: auto;">
                <div class="p-2 bg-light rounded mb-2">
                    <strong>HR Manager:</strong> Hello, John. Your application is under review.
                </div>
                <div class="p-2 bg-primary text-white rounded mb-2 text-end">
                    <strong>You:</strong> Thank you for the update. Looking forward to it!
                </div>
                <div class="p-2 bg-light rounded mb-2">
                    <strong>HR Manager:</strong> Please ensure your documents are up to date.
                </div>
                <div class="p-2 bg-primary text-white rounded mb-2 text-end">
                    <strong>You:</strong> Yes, I have uploaded the latest documents. Let me know if anything else is needed.
                </div>
            </div>
        </div>
        
        <div class="input-group">
            <input type="text" id="messageInput" class="form-control" placeholder="Type your message...">
            <button class="btn btn-primary" id="sendMessage"><i class="fa fa-paper-plane"></i> Send</button>
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
    <script src="../../Javasript/SNotifications.js"></script>
</body>
</html>
