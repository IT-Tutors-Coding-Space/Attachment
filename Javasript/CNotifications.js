document.addEventListener("DOMContentLoaded", function () {
    const notificationsTable = document.getElementById("notificationsTable");
    const clearNotificationsBtn = document.getElementById("clearNotificationsBtn");

    function markAsRead(event) {
        const row = event.target.closest("tr");
        const notificationId = row.dataset.id;
        const statusCell = row.querySelector("td:nth-child(2) span");

        fetch("CompanyNotifications.php?action=mark_read", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "id=" + encodeURIComponent(notificationId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                statusCell.classList.remove("bg-warning");
                statusCell.classList.add("bg-success");
                statusCell.textContent = "Read";
                event.target.disabled = true;
            }
        })
        .catch(error => console.error("Error marking as read:", error));
    }

    function deleteNotification(event) {
        const row = event.target.closest("tr");
        const notificationId = row.dataset.id;

        fetch("CompanyNotifications.php?action=delete", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "id=" + encodeURIComponent(notificationId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "deleted") {
                row.remove();
                if (notificationsTable.rows.length === 0) {
                    notificationsTable.innerHTML = "<tr><td colspan='3' class='text-center text-muted'>No notifications available</td></tr>";
                }
            }
        })
        .catch(error => console.error("Error deleting notification:", error));
    }

    function clearNotifications() {
        notificationsTable.innerHTML = "<tr><td colspan='3' class='text-center text-muted'>No notifications available</td></tr>";
    }

    notificationsTable.addEventListener("click", function (event) {
        if (event.target.classList.contains("mark-read")) {
            markAsRead(event);
        }
        if (event.target.classList.contains("delete-notification")) {
            deleteNotification(event);
        }
    });

    clearNotificationsBtn.addEventListener("click", clearNotifications);
});
