document.addEventListener("DOMContentLoaded", () => {
    console.log("Dashboard Page Loaded - New Version");
    
    // Fetch dashboard data
    fetch('/api/get-dashboard-data.php')
        .then(response => response.json())
        .then(data => {
            // Update application count
            const appCountEl = document.getElementById('applicationCount');
            if (appCountEl) appCountEl.textContent = data.applicationCount;

            // Update upcoming deadlines
            const deadlinesList = document.getElementById('upcomingDeadlines');
            if (deadlinesList) {
                deadlinesList.innerHTML = data.upcomingDeadlines
                    .map(deadline => `
                        <li class="flex justify-between py-2 border-b">
                            <span>${deadline.title}</span>
                            <span class="text-gray-600">${new Date(deadline.application_deadline).toLocaleDateString()}</span>
                        </li>
                    `)
                    .join('');
            }

            // Update notifications
            const notificationsList = document.getElementById('recentNotifications');
            if (notificationsList) {
                notificationsList.innerHTML = data.recentNotifications
                    .map(notification => `
                        <li class="py-2 border-b">
                            <p class="font-medium">${notification.title}</p>
                            <p class="text-sm text-gray-600">${notification.message}</p>
                            <p class="text-xs text-gray-400">${new Date(notification.created_at).toLocaleString()}</p>
                        </li>
                    `)
                    .join('');
            }
        })
        .catch(error => {
            console.error('Error fetching dashboard data:', error);
        });

    // Function to view application details
    function viewApplication(button) {
        const row = button.closest("tr");
        const opportunity = row.children[0].innerText;
        const company = row.children[1].innerText;
        const status = row.children[2].innerText;
        alert(`📄 Application Details:\n\n🔹 Opportunity: ${opportunity}\n🏢 Company: ${company}\n📌 Status: ${status}`);
    }

    // Function to withdraw an application
    function withdrawApplication(button) {
        if (confirm("⚠️ Are you sure you want to withdraw this application? This action cannot be undone.")) {
            const row = button.closest("tr");
            row.remove();
            alert("✅ Application withdrawn successfully!");
        }
    }

    // Function to track application progress
    function trackApplication(button) {
        const row = button.closest("tr");
        const status = row.children[2].innerText;
        
        if (status === "Accepted") {
            alert("🎉 Congratulations! Your application has been accepted. Check your messages for the next steps.");
        } else if (status === "Pending") {
            alert("⏳ Your application is still under review. Please be patient.");
        } else {
            alert("❌ Unfortunately, your application was rejected. Keep applying for more opportunities!");
        }
    }

    // Add event listeners dynamically
    const applicationsTable = document.getElementById("recentApplicationsTable");
    if (applicationsTable) {
        applicationsTable.addEventListener("click", (event) => {
            if (event.target.classList.contains("btn-outline-secondary")) {
                viewApplication(event.target);
            } else if (event.target.classList.contains("btn-outline-danger")) {
                withdrawApplication(event.target);
            } else if (event.target.classList.contains("btn-outline-info")) {
                trackApplication(event.target);
            }
        });
    }
});
