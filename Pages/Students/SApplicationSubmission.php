<?php
session_start();
require "../db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input fields
    if (empty($_POST["student_name"]) || empty($_POST["student_email"]) || empty($_POST["job_title"]) || empty($_POST["application_details"])) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    // Validate email format
    if (!filter_var($_POST["student_email"], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email format."]);
        exit();
    }

    $student_name = $_POST["student_name"];
    $student_email = $_POST["student_email"];
    $job_title = $_POST["job_title"];
    $application_details = $_POST["application_details"];

    try {
        $stmt = $conn->prepare("INSERT INTO applications (student_name, student_email, job_title, application_details, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$student_name, $student_email, $job_title, $application_details]);

        echo json_encode(["success" => true, "message" => "Application submitted successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
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
</head>
<body>
    <div class="container mt-5">
        <h2>Submit Your Application</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="student_name" class="form-label">Your Name</label>
                <input type="text" class="form-control" name="student_name" required>
            </div>
            <div class="mb-3">
                <label for="student_email" class="form-label">Your Email</label>
                <input type="email" class="form-control" name="student_email" required>
            </div>
            <div class="mb-3">
                <label for="job_title" class="form-label">Job Title</label>
                <input type="text" class="form-control" name="job_title" required>
            </div>
            <div class="mb-3">
                <label for="application_details" class="form-label">Application Details</label>
                <textarea class="form-control" name="application_details" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Application</button>
        </form>
    </div>
</body>
</html>
