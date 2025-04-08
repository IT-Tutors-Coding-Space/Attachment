<?php
require_once '../../db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') { 
    header("Location: ../SignUps/Slogin.php");
    exit(); 
} 

    $student_id = $_SESSION['user_id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM applications WHERE student_id = ? ORDER BY submitted_at DESC");
        $stmt->execute([$student_id]);
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application History</title>
   
    <link rel="stylesheet" href="../../CSS/SHistory.css">


    <header>
        <div class="logo">
            <img src="../../Images/logo.png" alt="AttachME Logo">
            <h1>AttachME</h1>
            <link rel="stylesheet" href="../../CSS/SDashboard.css">

            <p>Bridging Students to Opportunities, Seamlessly.</p>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="SDashboard.html">Dashboard</a></li>
                <li><a href="SAbout.html">About</a></li>
                <li><a href="SBrowse.html">Browse</a></li>
                <!-- <li><a href="../Students/SStatus.html">Applications</a></li> -->
                <li class="dropdown">
                    <a href="#">Profile</a>
                    <ul class="dropdown-menu">
                        <!-- <li><a href="#view-profile">View</a></li> -->
                        <li><a href="#settings">Settings</a></li>
                        <li><a href="../Students/SHistory.html">History</a></li>

                        <li><a href="#logout">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>
</head>
<body>
    <div class="container">
        <h1>Application History</h1>

        <div id="history-list">
            <table>
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Position</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($applications)): ?>
                        <tr>
                            <td colspan="5">No applications found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($applications as $application): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($application['opportunity_id']); ?></td>
                                <td><?php echo htmlspecialchars($application['status']); ?></td>
                                <td><?php echo htmlspecialchars($application['submitted_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
            <!-- History will be dynamically populated here -->
        </div>

        <div>
            <a href="../Students/SStatus.html">Back to Application Status</a>
        </div>
    </div>

    <script src="../../Javascript/SHistory.js"></script>
</body>
</html>
