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
            </ul>
        </div>
    </nav>
    
    <h1>Chat with a company</h1>
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
            <div class="chat-box" id="messageList" style="max-height: 500px; overflow-y: auto;">
                <?php foreach ($messages as $message): 
                    $is_sender = ($message['sender_id'] == $student_id);
                    $stmt = $conn->prepare("SELECT company_name FROM companies WHERE company_id = ?");
                    $stmt->execute([$is_sender ? $message['receiver_id'] : $message['sender_id']]);
                    $company = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="p-2 mb-2 <?= $is_sender ? 'bg-primary text-white text-end' : 'bg-light' ?> rounded">
                    <strong><?= $is_sender ? 'You' : htmlspecialchars($company['company_name']) ?>:</strong>
                    <?= htmlspecialchars($message['message']) ?>
                    <div class="small text-muted">
                        <?= date('M j, g:i a', strtotime($message['sent_at'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <form id="messageForm" class="input-group">
            <input type="hidden" name="sender_id" value="<?= $student_id ?>">
            <select name="receiver_id" class="form-select" required>
                <option value="">Select company...</option>
                <?php
                $stmt = $conn->prepare("
                    SELECT DISTINCT c.company_id, c.company_name 
                    FROM applications a
                    JOIN opportunities o ON a.opportunities_id = o.opportunities_id
                    JOIN companies c ON o.company_id = c.company_id
                    WHERE a.student_id = ?
                    AND a.status IN ('submitted', 'under_review', 'accepted')
                ");
                $stmt->execute([$student_id]);
                $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($companies as $company): ?>
                    <option value="<?= $company['company_id'] ?>">
                        <?= htmlspecialchars($company['company_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
            <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Send</button>
        </form>
    </div>

    <script>
    document.getElementById('messageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('../../api/send-message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to send message'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the message');
        });
    });

    // Auto-refresh messages every 5 seconds
    setInterval(() => {
        location.reload();
    }, 5000);
    </script>
    
    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../Help Center.php" class="text-white fw-bold">Help Center</a>
            <a href="../Students/Terms of service.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../Students/Contact Support.php" class="text-white fw-bold">Contact Support:</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javasript/SNotifications.js"></script>
</body>
</html>
