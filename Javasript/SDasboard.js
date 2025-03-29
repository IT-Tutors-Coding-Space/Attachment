// student-dashboard.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Student Dashboard Loaded");

    const totalApplicationsElement = document.getElementById("totalApplications");
    const acceptedApplicationsElement = document.getElementById("acceptedApplications");
    const pendingApplicationsElement = document.getElementById("pendingApplications");
    const applicationsTable = document.getElementById("recentApplicationsTable");

    // Fetch student dashboard data from the server
    async function fetchDashboardData() {
        try {
            const response = await fetch("../../api/student-dashboard.php");
            if (!response.ok) {
                throw new Error("Failed to fetch dashboard data");
            }
            const data = await response.json();

            // Update dashboard stats
            totalApplicationsElement.innerText = data.totalApplications;
            acceptedApplicationsElement.innerText = data.acceptedApplications;
            pendingApplicationsElement.innerText = data.pendingApplications;

            // Populate recent applications table
            applicationsTable.innerHTML = "";
            data.recentApplications.forEach(application => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${application.opportunity_title}</td>
                    <td>${application.company_name}</td>
                    <td><span class="badge bg-${application.status === "Accepted" ? "success" : (application.status === "Pending" ? "warning" : "danger")}">${application.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary">View</button>
                    </td>
                `;
                applicationsTable.appendChild(row);
            });
        } catch (error) {
            console.error("Error fetching dashboard data:", error);
            alert("Failed to load dashboard data. Please try again later.");
        }
    }

    // Fetch data on page load
    fetchDashboardData();

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
        let totalApps = parseInt(totalApplicationsElement.innerText);
        let pendingApps = parseInt(pendingApplicationsElement.innerText);

        totalApps += change;
        pendingApps += change;

        totalApplicationsElement.innerText = totalApps;
        pendingApplicationsElement.innerText = pendingApps;
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
});
