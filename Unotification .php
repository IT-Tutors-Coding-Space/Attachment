<script>
    function loadNotifications() {
        fetch('notifications.php?action=fetch')
            .then(response => response.json())
            .then(data => {
                let notificationArea = document.getElementById('notifications');
                notificationArea.innerHTML = "";

                if (data.length === 0) {
                    notificationArea.innerHTML = "<p class='text-muted'>No notifications available.</p>";
                    return;
                }

                data.forEach(notification => {
                    notificationArea.innerHTML += `
                        <div class="border p-2 mb-2">
                            <p>${notification.message} - ${notification.is_read ? 'Read' : 'Unread'}</p>
                            <button onclick="markAsRead(${notification.id})" class="btn btn-success btn-sm">Mark as Read</button>
                            <button onclick="deleteNotification(${notification.id})" class="btn btn-danger btn-sm">Delete</button>
                        </div>
                    `;
                });
            });
    }

    function markAsRead(id) {
        fetch('notifications.php?action=mark_read', {
            method: 'POST',
            body: new URLSearchParams({ 'id': id })
        }).then(() => loadNotifications());
    }

    function deleteNotification(id) {
        fetch('notifications.php?action=delete', {
            method: 'POST',
            body: new URLSearchParams({ 'id': id })
        }).then(() => loadNotifications());
    }

    window.onload = loadNotifications;
</script>
