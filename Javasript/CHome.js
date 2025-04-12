 // Function to fetch and update opportunity statistics
async function updateOpportunityStats() {
    try {
        const response = await fetch('../../api/getOpportunityStats.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include'
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        
        if (data.success) {
            // Update the statistics tables
            document.getElementById('totalOpportunitiesCount').textContent = data.totalOpportunities || 0;
            document.getElementById('totalApplicationsCount').textContent = data.totalApplications || 0;
            document.getElementById('acceptedApplicationsCount').textContent = data.acceptedApplications || 0;
            document.getElementById('rejectedApplicationsCount').textContent = data.rejectedApplications || 0;
            document.getElementById('pendingApplicationsCount').textContent = data.pendingApplications || 0;
            
            // Add animation effect
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach(card => {
                card.classList.add('animate__animated', 'animate__pulse');
                setTimeout(() => {
                    card.classList.remove('animate__animated', 'animate__pulse');
                }, 1000);
            });
        }
    } catch (error) {
        console.error('Error fetching opportunity stats:', error);
    }
}

// Initialize when page loads
document.addEventListener("DOMContentLoaded", function() {
    // Set interval to update stats periodically (every 30 seconds)
    setInterval(updateOpportunityStats, 30000);
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.boxShadow = '';
        });
    });
});

// Function to show alert messages
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
            <div>${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alertDiv);
            bsAlert.close();
        }, 5000);
    }
}