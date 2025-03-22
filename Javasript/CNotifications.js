document.addEventListener("DOMContentLoaded", function () {
    const notificationsTable = document.getElementById("notificationsTable");
    const clearNotificationsBtn = document.getElementById("clearNotificationsBtn");
    
    function markAsRead(event) {
        const row = event.target.closest("tr");
        const statusCell = row.querySelector("td:nth-child(2) span");
        statusCell.classList.remove("bg-warning");
        statusCell.classList.add("bg-success");
        statusCell.textContent = "Read";
        event.target.disabled = true;
    }
    
    function deleteNotification(event) {
        const row = event.target.closest("tr");
        row.remove();
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
