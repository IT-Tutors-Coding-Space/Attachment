<?php
require_once "../db.php";
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance | AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .maintenance-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .maintenance-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            color: #fff;
        }
        h1 {
            font-weight: 700;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        .progress {
            height: 8px;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.2);
        }
        .progress-bar {
            background-color: white;
            width: 75%;
            animation: progress 2s ease-in-out infinite;
        }
        @keyframes progress {
            0% { width: 0%; }
            50% { width: 75%; }
            100% { width: 0%; }
        }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <div class="maintenance-icon">
            <i class="fas fa-tools"></i>
        </div>
        <h1>System Maintenance</h1>
        <p>We're currently performing scheduled maintenance to improve your experience. The system will be back online shortly.</p>
        
        <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated"></div>
        </div>
        
        <p class="small">Estimated completion time: <?= date('H:i A', time() + 3600) ?></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
