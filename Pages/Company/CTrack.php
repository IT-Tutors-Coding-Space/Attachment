
<?php
// --- BEGIN PHP BACKEND LOGIC ---

session_start();
require_once "../../db.php"; // Ensure this path is correct relative to this file's location

// Set content type for JSON responses *early* in case of errors before HTML
header('Content-Type: application/json');

// --- Authentication & Authorization Check ---
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    // If the request is likely an AJAX call, send JSON error
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        http_response_code(403); // Forbidden
        echo json_encode(["success" => false, "message" => "Unauthorized access."]);
        exit();
    } else {
        // If it's a direct browser access, redirect to login or show an error page
        // For simplicity here, we'll just exit, but a redirect is better UX
        // header('Location: /login.php'); // Example redirect
        die("Unauthorized access. Please log in as a company."); // Simple exit for non-AJAX
    }
}

$companyId = $_SESSION["user_id"];

// --- Handle POST Requests (API Actions) ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    // --- Action: Create Notification ---
    if ($action === "create") {
        // Validate input
        if (empty($_POST["message"])) {
            http_response_code(400); // Bad Request
            echo json_encode(["success" => false, "message" => "Message cannot be empty."]);
            exit();
        }

        $message = trim($_POST["message"]); // Trim whitespace

        try {
            $stmt = $conn->prepare("INSERT INTO notifications (company_id, message, is_read, created_at) VALUES (:company_id, :message, 0, NOW())");
            $stmt->execute([
                ':company_id' => $companyId,
                ':message' => $message
            ]);

            // Check if insert was successful
            if ($stmt->rowCount() > 0) {
                 echo json_encode(["success" => true, "message" => "Notification created."]);
            } else {
                 http_response_code(500); // Internal Server Error
                 echo json_encode(["success" => false, "message" => "Failed to create notification."]);
            }
        } catch (PDOException $e) {
            http_response_code(500); // Internal Server Error
            // Log error: error_log("Database Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Database error occurred."]); // Keep detailed errors out of public responses
        }
        exit(); // Crucial: Stop script execution after handling POST action
    }

    // --- Action: Mark Notification as Read ---
    elseif ($action === "mark_read") {
        $id = $_POST["id"] ?? null;

        // Validate ID
        if (!$id || !ctype_digit((string)$id)) { // Ensure it's a positive integer string
            http_response_code(400); // Bad Request
            echo json_encode(["success" => false, "message" => "Invalid notification ID."]);
            exit();
        }

        try {
            // *** Corrected SQL: Use 'id' for the notification's primary key ***
            $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id AND company_id = :company_id");
            $stmt->execute([':id' => $id, ':company_id' => $companyId]);

            // Check if any row was actually updated
             if ($stmt->rowCount() > 0) {
                 echo json_encode(["success" => true, "message" => "Notification marked as read."]);
             } else {
                 // Could be because ID doesn't exist, or already marked read, or doesn't belong to user
                 echo json_encode(["success" => false, "message" => "Notification not found or already marked as read."]);
             }

        } catch (PDOException $e) {
            http_response_code(500);
            // Log error: error_log("Database Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Database error occurred."]);
        }
        exit(); // Crucial: Stop script execution
    }

    // --- Action: Delete Notification ---
    elseif ($action === "delete") {
        $id = $_POST["id"] ?? null;

        // Validate ID
        if (!$id || !ctype_digit((string)$id)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid notification ID."]);
            exit();
        }

        try {
            $stmt = $conn->prepare("DELETE FROM notifications WHERE id = :id AND company_id = :company_id");
            $stmt->execute([':id' => $id, ':company_id' => $companyId]);

            // Check if any row was actually deleted
            if ($stmt->rowCount() > 0) {
                echo json_encode(["success" => true, "message" => "Notification deleted."]);
            } else {
                // Could be because ID doesn't exist or doesn't belong to user
                 echo json_encode(["success" => false, "message" => "Notification not found."]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            // Log error: error_log("Database Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Database error occurred."]);
        }
        exit(); // Crucial: Stop script execution
    }

    // --- Action: Clear All Notifications ---
    elseif ($action === "clear_all") {
        try {
            $stmt = $conn->prepare("DELETE FROM notifications WHERE company_id = :company_id");
            $stmt->execute([':company_id' => $companyId]);

            // rowCount tells how many were deleted
            $deletedCount = $stmt->rowCount();
            echo json_encode(["success" => true, "message" => "$deletedCount notification(s) cleared."]);

        } catch (PDOException $e) {
            http_response_code(500);
            // Log error: error_log("Database Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Database error occurred while clearing notifications."]);
        }
        exit(); // Crucial: Stop script execution
    }

    // --- Handle Invalid Action ---
    else {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Invalid action specified."]);
        exit();
    }
}

// --- Handle GET Requests (Fetch Notifications for AJAX) ---
// This specifically handles the JavaScript fetch request, not the initial page load
elseif ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    try {
        $stmt = $conn->prepare("SELECT id, message, is_read, created_at FROM notifications WHERE company_id = :company_id ORDER BY created_at DESC");
        $stmt->execute([':company_id' => $companyId]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure created_at is in a consistent format (like ISO 8601) for JS Date parsing
        foreach ($notifications as &$notification) {
            if (isset($notification['created_at'])) {
                 $dateTime = new DateTime($notification['created_at']);
                 $notification['created_at'] = $dateTime->format(DateTime::ATOM); // e.g., 2023-10-27T10:30:00+00:00
            }
        }
        unset($notification); // Unset reference

        // Reset content type header in case it was changed by error handling above
        header('Content-Type: application/json');
        echo json_encode(["success" => true, "data" => $notifications]);

    } catch (PDOException $e) {
        http_response_code(500);
        // Log error: error_log("Database Error: " . $e->getMessage());
        // Reset content type header
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Database error occurred while fetching notifications."]);
    }
    exit(); // Crucial: Stop script execution after sending JSON data
}

// --- If the script reaches here, it means it's a direct GET request for the page ---
// --- Reset Content-Type for HTML Output ---
header('Content-Type: text/html; charset=UTF-8');

// --- END PHP BACKEND LOGIC ---
?>
<!-- --- BEGIN HTML STRUCTURE --- -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Company Notifications - AttachME</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>

  <!-- Tailwind (Ensure it doesn't conflict heavily with Bootstrap if used extensively) -->
  <!-- Using Tailwind utility classes alongside Bootstrap components -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Optional Custom CSS -->
  <!-- <link rel="stylesheet" href="css/company.css"/> -->

  <style>
      /* Optional: Add minor custom styles if needed */
      body {
          display: flex;
          flex-direction: column;
          min-height: 100vh;
      }
      .flex-grow-1 {
          flex-grow: 1;
      }
       /* Style for better table appearance on smaller screens */
       .table-responsive {
           margin-bottom: 1rem; /* Add some space below the table */
       }
  </style>
</head>
<body class="bg-gray-100">

  <!-- Navbar -->
  <nav class="navbar navbar-dark bg-dark shadow-lg p-3">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold text-white" href="dashboard.php"> <!-- Link to company dashboard -->
          <i class="fas fa-building"></i> AttachME - Company Portal
      </a>
      <span class="navbar-text text-white">
        Notifications
      </span>
      <!-- Optional: Add logout button or user menu here -->
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container py-5 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <h4 class="fw-bold text-primary mb-0"><i class="fas fa-bell"></i> Your Notifications</h4>
      <button class="btn btn-sm btn-danger" id="clearAllBtn">
          <i class="fa fa-trash-alt"></i> Clear All Notifications
      </button>
    </div>

    <!-- Notification Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-dark">
                  <tr>
                    <th scope="col">Message</th>
                    <th scope="col" class="text-center">Status</th>
                    <th scope="col">Received At</th>
                    <th scope="col" class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody id="notificationsTable">
                  <!-- Placeholder row while loading -->
                  <tr>
                      <td colspan="4" class="text-center text-muted">
                          <div class="spinner-border spinner-border-sm" role="status">
                              <span class="visually-hidden">Loading...</span>
                          </div>
                          Loading notifications...
                      </td>
                  </tr>
                  <!-- Notifications will be dynamically injected here by JavaScript -->
                </tbody>
              </table>
            </div>
        </div>
         <div class="card-footer text-muted" id="notification-summary">
              <!-- Summary like "Showing X notifications" can be added here -->
              Loading summary...
          </div>
    </div>
  </div> <!-- End Container -->

  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-3 mt-auto">
    <p class="mb-0">© <?php echo date("Y"); ?> AttachME. All rights reserved.</p> <!-- Dynamic year -->
    <div class="d-flex justify-content-center gap-4 mt-2">
      <!-- Use relative or absolute paths as appropriate -->
      <a href="../common/help-center.php" class="text-white fw-bold">Help Center</a>
      <a href="../common/terms.php" class="text-white fw-bold">Terms</a>
      <a href="../common/contact.php" class="text-white fw-bold">Support: 0700234362</a>
    </div>
  </footer>

  <!-- --- BEGIN JAVASCRIPT LOGIC --- -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // --- Global Elements ---
    const notificationsTableBody = document.getElementById("notificationsTable");
    const clearAllBtn = document.getElementById("clearAllBtn");
    const notificationSummary = document.getElementById("notification-summary");

    // --- Function: Display Message (e.g., loading, error) ---
    function displayTableMessage(message, type = 'info') {
        let cssClass = 'text-muted';
        if (type === 'error') cssClass = 'text-danger fw-bold';
        if (type === 'empty') cssClass = 'text-secondary';

        notificationsTableBody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center ${cssClass}">${message}</td>
            </tr>
        `;
        notificationSummary.textContent = ''; // Clear summary on message display
    }

    // --- Function: Render Notifications ---
    function renderNotifications(notifications) {
        notificationsTableBody.innerHTML = ""; // Clear existing rows

        if (!notifications || notifications.length === 0) {
            displayTableMessage("No notifications found.", 'empty');
            notificationSummary.textContent = "You have 0 notifications.";
            clearAllBtn.disabled = true; // Disable clear button if no notifications
            return;
        }

        clearAllBtn.disabled = false; // Enable clear button

        let unreadCount = 0;
        notifications.forEach((n) => {
            const isRead = Boolean(n.is_read); // Ensure boolean type
            if (!isRead) {
                unreadCount++;
            }

            const row = document.createElement("tr");
            row.classList.toggle('table-light', !isRead); // Highlight unread slightly

            // Format date nicely
            let formattedDate = 'Invalid Date';
            try {
                 const dateObj = new Date(n.created_at);
                 if (!isNaN(dateObj)) {
                     formattedDate = dateObj.toLocaleString(undefined, { // Use user's locale
                         dateStyle: 'medium',
                         timeStyle: 'short'
                    });
                 }
            } catch(e) { console.error("Error parsing date:", n.created_at, e); }


            row.innerHTML = `
                <td class="text-break">${escapeHTML(n.message)}</td>
                <td class="text-center">
                    <span class="badge ${isRead ? 'bg-success' : 'bg-warning text-dark'}">
                      ${isRead ? 'Read' : 'Unread'}
                    </span>
                </td>
                <td style="min-width: 150px;">${formattedDate}</td>
                <td class="text-center" style="min-width: 180px;">
                  ${isRead
                    ? `<button class="btn btn-secondary btn-sm" disabled title="Already read"><i class="fa fa-check"></i> Read</button>`
                    : `<button class="btn btn-success btn-sm" onclick="markAsRead(${n.id})" title="Mark as Read"><i class="fa fa-check"></i> Mark Read</button>`}
                  <button class="btn btn-danger btn-sm ms-1" onclick="deleteNotification(${n.id})" title="Delete Notification"><i class="fa fa-trash"></i> Delete</button>
                </td>
            `;
            notificationsTableBody.appendChild(row);
        });

        // Update summary
        const totalCount = notifications.length;
        notificationSummary.textContent = `Showing ${totalCount} notification(s). ${unreadCount} unread.`;
    }

     // --- Utility: Escape HTML to prevent XSS ---
     function escapeHTML(str) {
        if (str === null || str === undefined) return '';
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
     }


    // --- Function: Fetch Notifications from Backend ---
    async function loadNotifications() {
        displayTableMessage('<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div> Loading notifications...', 'info');
        clearAllBtn.disabled = true; // Disable button while loading

        try {
            // Fetch calls the *same file*, but the PHP checks for XHR header
            const response = await fetch("CompanyNotifications.php", {
                 method: 'GET',
                 headers: {
                    'X-Requested-With': 'XMLHttpRequest', // Important header for backend check
                    'Accept': 'application/json'
                 }
             });

            if (!response.ok) {
                // Try to get error message from response body if possible
                let errorMsg = `Error: ${response.status} ${response.statusText}`;
                try {
                    const errorData = await response.json();
                    if (errorData && errorData.message) {
                        errorMsg = errorData.message;
                    }
                } catch(e) { /* Ignore if response body isn't valid JSON */ }
                throw new Error(errorMsg);
            }

            const data = await response.json();

            if (data.success && data.data) {
                renderNotifications(data.data);
            } else {
                // Use message from backend if provided, otherwise generic error
                const message = data.message || "Failed to load notifications.";
                displayTableMessage(message, 'error');
                notificationSummary.textContent = 'Error loading notifications.';
            }
        } catch (error) {
            console.error("Fetch Error:", error);
            displayTableMessage(`Error loading notifications: ${error.message}`, 'error');
            notificationSummary.textContent = 'Error loading notifications.';
        } finally {
             // Re-enable button unless data is empty (handled in renderNotifications)
             if (notificationsTableBody.querySelector('tr td[colspan="4"]') === null) {
                 clearAllBtn.disabled = false;
             }
        }
    }

    // --- Function: Mark a Notification as Read ---
    async function markAsRead(id) {
        if (!id) return;
        console.log(`Attempting to mark notification ${id} as read...`); // Debug log
        try {
            const response = await fetch("CompanyNotifications.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: `action=mark_read&id=${id}`
            });

             const result = await response.json();
             console.log("Mark as read response:", result); // Debug log

             if (!response.ok || !result.success) {
                 alert(`Failed to mark as read: ${result.message || 'Unknown error'}`);
             }
             // Reload notifications regardless of success/failure to show current state
             await loadNotifications();

        } catch(error) {
             console.error("Mark as Read Error:", error);
             alert(`An error occurred: ${error.message}`);
              await loadNotifications(); // Reload even on fetch error
        }
    }

    // --- Function: Delete a Notification ---
    async function deleteNotification(id) {
        if (!id) return;
        // Confirmation dialog
        if (!confirm(`Are you sure you want to delete this notification?`)) {
            return; // User cancelled
        }

        console.log(`Attempting to delete notification ${id}...`); // Debug log
        try {
            const response = await fetch("CompanyNotifications.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                     'X-Requested-With': 'XMLHttpRequest',
                     'Accept': 'application/json'
                },
                body: `action=delete&id=${id}`
            });

            const result = await response.json();
             console.log("Delete response:", result); // Debug log

             if (!response.ok || !result.success) {
                 alert(`Failed to delete: ${result.message || 'Unknown error'}`);
             }
             // Always reload to reflect changes
             await loadNotifications();

         } catch(error) {
             console.error("Delete Error:", error);
             alert(`An error occurred: ${error.message}`);
             await loadNotifications(); // Reload even on fetch error
         }
    }

    // --- Event Listener: Clear All Notifications ---
    clearAllBtn.addEventListener("click", async () => {
        if (confirm("Are you sure you want to clear ALL notifications? This action cannot be undone.")) {
            console.log("Attempting to clear all notifications..."); // Debug log
            try {
                 const response = await fetch("CompanyNotifications.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: `action=clear_all`
                 });

                 const result = await response.json();
                 console.log("Clear All response:", result); // Debug log

                 if (!response.ok || !result.success) {
                     alert(`Failed to clear notifications: ${result.message || 'Unknown error'}`);
                 } else {
                     alert(result.message || "All notifications cleared."); // Show success message from backend
                 }
                 // Always reload to show the empty table (or any remaining errors)
                 await loadNotifications();

            } catch(error) {
                console.error("Clear All Error:", error);
                alert(`An error occurred while clearing notifications: ${error.message}`);
                 await loadNotifications(); // Reload even on fetch error
            }
        }
    });

    // --- Initial Load ---
    // Use DOMContentLoaded for faster perceived load than window.onload
    document.addEventListener('DOMContentLoaded', loadNotifications);

  </script>
  <!-- --- END JAVASCRIPT LOGIC --- -->

</body>
</html>
<!-- --- END HTML STRUCTURE --- -->