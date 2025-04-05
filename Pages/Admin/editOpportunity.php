<?php
require_once "../../db.php";
session_start();

// Check if the user is logged in as admin
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: Alogin.php");
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log all received POST data for debugging
    error_log("Received POST data: " . print_r($_POST, true));
    
    // Verify all required fields are present
    $required_fields = ['opportunities_id', 'title', 'description', 'location', 'application_deadline', 'available_slots', 'status'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            error_log("Missing required field: $field");
            header("Location: AOpportunities.php?error=Missing required field: $field");
            exit();
        }
    }

    $opportunities_id = $_POST['opportunities_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $application_deadline = $_POST['application_deadline'];
    $available_slots = $_POST['available_slots'];
    $status = $_POST['status'];

    try {
        // Verify database connection
        if (!$conn) {
            error_log("Database connection failed");
            throw new PDOException("Database connection failed");
        }

        // Log table structure for debugging
        $stmt = $conn->query("DESCRIBE opportunities");
        $tableStructure = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Opportunities table structure: " . print_r($tableStructure, true));

        // Log all values before update
        error_log("Updating opportunity ID: $opportunities_id with values: " . 
                 "title=$title, description=$description, location=$location, " .
                 "deadline=$application_deadline, slots=$available_slots, status=$status");

        $stmt = $conn->prepare("UPDATE opportunities SET 
            title = :title,
            description = :description,
            location = :location,
            application_deadline = :application_deadline,
            available_slots = :available_slots,
            status = :status
            WHERE opportunities_id = :opportunities_id");

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':application_deadline', $application_deadline);
        $stmt->bindParam(':available_slots', $available_slots);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':opportunities_id', $opportunities_id);
        
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            error_log("Update failed: " . print_r($errorInfo, true));
            throw new PDOException("Update failed: " . $errorInfo[2]);
        }

        // Verify if update was successful
        $rowsAffected = $stmt->rowCount();
        error_log("Rows affected by update: $rowsAffected");
        
        if ($rowsAffected === 0) {
            error_log("No rows affected - opportunity ID $opportunities_id may not exist or no changes were made");
            header("Location: AOpportunities.php?error=Opportunity not found or no changes made");
            exit();
        }

        error_log("Successfully updated opportunity ID: $opportunities_id");
        header("Location: AOpportunities.php?success=Opportunity updated successfully");
        exit();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        header("Location: AOpportunities.php?error=Database error: " . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: AOpportunities.php");
    exit();
}
?>
