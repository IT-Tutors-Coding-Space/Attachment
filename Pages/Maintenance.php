<?php
require_once "../db.php";
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Maintenance Mode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .maintenance-container { margin-top: 100px; text-align: center; }
    </style>
</head>
<body>
    <div class="container maintenance-container">
        <h1 class="text-danger">🚧 Maintenance Mode 🚧</h1>
        <p class="lead">We're currently performing scheduled maintenance</p>
        <p>Please check back later. We apologize for any inconvenience.</p>
        <?php if (isset($_SESSION['admin_id'])): ?>
            <a href="Admin/ASettings.php" class="btn btn-primary">Admin Dashboard</a>
        <?php endif; ?>
    </div>
</body>
</html>
