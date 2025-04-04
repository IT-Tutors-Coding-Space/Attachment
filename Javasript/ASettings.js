document.addEventListener('DOMContentLoaded', function() {
    // Confirm before logging out all devices
    document.querySelector('button[name="logout_all"]')?.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to log out from all other devices?')) {
            e.preventDefault();
        }
    });

    // Toggle maintenance mode warning
    document.getElementById('maintenanceMode')?.addEventListener('change', function() {
        if (this.checked) {
            alert('Warning: Enabling maintenance mode will restrict access to all users except administrators.');
        }
    });
});
