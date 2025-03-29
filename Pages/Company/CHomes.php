<?php
$host = "localhost";
$dbname = "attachme"; 
$username = "root";
$password = "Attachme@Admin";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
// Function to get total opportunities
function getTotalOpportunities($conn) {
    $sql = "SELECT COUNT(*) as total FROM opportunity WHERE company_id = " . $_SESSION['company_id']; // Assuming you store company_id in session
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    } else {
        return 0;
    }
}

// Function to get total applications
function getTotalApplications($conn) {
    $sql = "SELECT COUNT(*) as total FROM application WHERE opportunity_id IN (SELECT id FROM opportunity WHERE company_id = " . $_SESSION['company_id'] . ")";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    } else {
        return 0;
    }
}

// Function to get pending applications
function getPendingApplications($conn) {
    $sql = "SELECT COUNT(*) as total FROM application WHERE opportunity_id IN (SELECT id FROM opportunity WHERE company_id = " . $_SESSION['company_id'] . ") AND status = 'Pending'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    } else {
        return 0;
    }
}

// Function to get recent applications
function getRecentApplications($conn) {
    $sql = "SELECT applications.id as application_id, students.name as full_name, opportunitY.title as title, application.status 
            FROM applications 
            JOIN students ON application.student_id = students.id
            JOIN opportunity ON application.opportunity_id = opportunity.id
            WHERE opportunity.company_id = " . $_SESSION['company_id'];
    $result = $conn->query($sql);
    $applications = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {      
            $applications[] = $row;
        }
    }
    return $applications;
}

// Start the session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Ensure company_id is set in the session
if (!isset($_SESSION['company_id'])) {
    // Redirect to login or handle the error
    header("Location: ../login.php"); // Example redirect
    exit;
}

//Get data from database
$totalOpportunities = getTotalOpportunities($conn);
$totalApplications = getTotalApplications($conn);
$pendingApplications = getPendingApplications($conn);
$recentApplications = getRecentApplications($conn);

$conn->close();
?>