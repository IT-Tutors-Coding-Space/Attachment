// CHome.js - Company Dashboard Functionality

document.addEventListener("DOMContentLoaded", () => {
    console.log("Company Dashboard Loaded");

    // Display status message if present in URL
    const urlParams = new URLSearchParams(window.location.search);
    const statusMessage = urlParams.get('message');
    if (statusMessage) {
        showStatusMessage(decodeURIComponent(statusMessage), urlParams.get('isError') === 'true');
    }
});

function confirmAction(action) {
    return confirm(`Are you sure you want to ${action} this application?`);
}

function showStatusMessage(message, isError = false) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert alert-${isError ? 'danger' : 'success'} alert-dismissible fade show`;
    messageDiv.role = 'alert';
    messageDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const container = document.querySelector('.main-content');
    if (container) {
        container.insertBefore(messageDiv, container.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = bootstrap.Alert.getOrCreateInstance(messageDiv);
            alert.close();
        }, 5000);
    }
}
