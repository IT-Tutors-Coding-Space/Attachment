// student-dashboard.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Student Dashboard Loaded");

    // Sample data for dashboard stats
    document.getElementById("totalApplications").innerText = "5";
    document.getElementById("acceptedApplications").innerText = "2";
    document.getElementById("pendingApplications").innerText = "3";

    const applicationsTable = document.getElementById("recentApplicationsTable");

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
            updateDashboardStats(-1);
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

    // Function to update dashboard stats dynamically
    function updateDashboardStats(change) {
        let totalApps = parseInt(document.getElementById("totalApplications").innerText);
        let pendingApps = parseInt(document.getElementById("pendingApplications").innerText);

        totalApps += change;
        pendingApps += change;

        document.getElementById("totalApplications").innerText = totalApps;
        document.getElementById("pendingApplications").innerText = pendingApps;
    }

    // Function to refresh applications list dynamically
    function refreshApplications() {
        alert("🔄 Fetching latest application updates...");
        location.reload();
    }

    // Add event listeners dynamically
    applicationsTable.addEventListener("click", (event) => {
        if (event.target.classList.contains("btn-outline-secondary")) {
            viewApplication(event.target);
        } else if (event.target.classList.contains("btn-outline-danger")) {
            withdrawApplication(event.target);
        } else if (event.target.classList.contains("btn-outline-info")) {
            trackApplication(event.target);
        }
    });

//     // Auto-refresh applications every 30 seconds
//     setInterval(refreshApplications, 30000);
});
