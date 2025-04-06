<?php
session_start();
require "../../db.php";

// Check if user is logged in as student
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../../SignUps/Slogin.php");
    exit();
}

// Get student info from session
$student_id = $_SESSION["user_id"];
$opportunity_id = $_GET["opportunity_id"] ?? null;

// Get opportunity details if ID is provided
$opportunity = null;
if ($opportunity_id) {
    try {
        $stmt = $conn->prepare("SELECT title, company_name FROM opportunities WHERE opportunities_id = ?");
        $stmt->execute([$opportunity_id]);
        $opportunity = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error fetching opportunity details: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $application_details = $_POST["application_details"] ?? '';
    $opportunity_id = $_POST["opportunities_id"] ?? null;

    try {
        // Insert application
        $stmt = $conn->prepare("
            INSERT INTO applications 
            (student_id, opportunities_id, application_details, status, created_at) 
            VALUES (?, ?, ?, 'Pending', NOW())
        ");
        $stmt->execute([$student_id, $opportunity_id, $application_details]);

        $_SESSION["success_message"] = "Application submitted successfully!";
        header("Location: SApplicationSubmission.php?success=1");
        exit();
    } catch (PDOException $e) {
        $error = "Error submitting application: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submission - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../Javasript/SApplicationSubmission.js"></script>
</head>
<body>
    <div class="container mt-5">
        <?php if (isset($_GET["success"])): ?>
            <div class="alert alert-success">
                <?= $_SESSION["success_message"] ?? "Application submitted successfully!" ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <h2>Submit Your Application</h2>
        <?php if ($opportunity): ?>
            <div class="alert alert-info mb-4">
                <h4>Applying for: <?= htmlspecialchars($opportunity["title"]) ?></h4>
                <p>Company: <?= htmlspecialchars($opportunity["company_name"]) ?></p>
            </div>
        <?php endif; ?>

        <form id="applicationForm" method="POST" action="">
            <input type="hidden" name="opportunity_id" value="<?= htmlspecialchars($opportunity_id) ?>">
            
            <div class="mb-3">
                <label class="form-label">Application Details</label>
                <textarea class="form-control" name="application_details" required 
                    placeholder="Explain why you're a good fit for this opportunity..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Submit Application</button>
            <a href="SBrowse.php" class="btn btn-secondary">Back to Opportunities</a>
        </form>
    </div>
</body>
</html>
