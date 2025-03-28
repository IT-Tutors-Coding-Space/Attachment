<?php
session_start();
require "../db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = $_POST["full_name"];
    $email = $_POST["email"];
    $role = $_POST["role"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    try {
        $conn->beginTransaction();

        // Insert into the admins table
        $stmt = $conn->prepare("INSERT INTO admins (full_name, email, role, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$fullName, $email, $role, $password]);
        $admin_id = $conn->lastInsertId(); // Get the newly inserted admin_id

        // Ensure the admin_id exists before inserting into the users table
        if ($admin_id) {
            // Insert into the users table with the admin_id
            $stmt = $conn->prepare("INSERT INTO users (admin_id, email, password_hash, role, created_at) VALUES (?, ?, ?, 'admin', NOW())");
            $stmt->execute([$admin_id, $email, $password]);
        } else {
            throw new Exception("Failed to retrieve admin_id after inserting into admins table.");
        }

        $conn->commit();

        // Redirect to login page after successful registration
        header("Location: Loginn.php");
        exit();
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
