<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: ../SignUps/Clogin.php");
    exit();
}

$company_id = $_SESSION['user_id'];

// Get all messages for this company
try {
    $stmt = $conn->prepare("
        SELECT m.*, s.full_name as student_name
        FROM messages m
        JOIN students s ON (m.sender_id = s.student_id OR m.receiver_id = s.student_id) 
        WHERE m.sender_id = ? OR m.receiver_id = ?
        ORDER BY m.sent_at DESC
    ");
    $stmt->execute([$company_id, $company_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $messages = [];
    $error = "Error loading messages: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .message-container {
            max-height: 500px;
            overflow-y: auto;
        }
        .message-bubble {
            max-width: 70%;
            border-radius: 1rem;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
        }
        .sent {
            background-color: #007bff;
            color: white;
            margin-left: auto;
        }
        .received {
            background-color: #e9ecef;
            margin-right: auto;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid">
            <h2 class="text-white fw-bold fs-3">AttachME - Company Portal</h2>
        </div>
    </nav>

    <div class="container py-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Company Messages</h3>
            </div>
            <div class="card-body">
                <div class="message-container mb-3">
                    <?php foreach ($messages as $message): ?>
                        <div class="d-flex flex-column <?= $message['sender_id'] == $company_id ? 'align-items-end' : 'align-items-start' ?>">
                            <small class="text-muted">
                                <?= htmlspecialchars($message['sender_id'] == $company_id ? 'You' : $message['student_name']) ?>
                                to 
                                <?= htmlspecialchars($message['receiver_id'] == $company_id ? 'You' : $message['student_name']) ?>
                                - <?= date('M j, g:i a', strtotime($message['sent_at'])) ?>
                            </small>
                            <div class="message-bubble <?= $message['sender_id'] == $company_id ? 'sent' : 'received' ?>">
                                <?= htmlspecialchars($message['message']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form id="messageForm" class="mt-3">
                    <input type="hidden" name="sender_id" value="<?= $company_id ?>">
                    <div class="input-group mb-3">
                        <select name="receiver_id" class="form-select" required>
                            <option value="">Select student...</option>
                            <?php
                            // Get students who have applied to this company's opportunities
                            $stmt = $conn->prepare("
                                SELECT DISTINCT s.student_id, s.full_name 
                                FROM applications a
                                JOIN students s ON a.student_id = s.student_id
                                JOIN opportunities o ON a.opportunities_id = o.opportunities_id
                                WHERE o.company_id = ?
                                AND a.status IN ('submitted', 'under_review', 'accepted')
                            ");
                            $stmt->execute([$company_id]);
                            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($students as $student): ?>
                                <option value="<?= $student['student_id'] ?>">
                                    <?= htmlspecialchars($student['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <textarea name="message" class="form-control" placeholder="Type your message..." required></textarea>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
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
</body>

</html>