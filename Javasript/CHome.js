// company-dashboard.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Company Dashboard Loaded");

    // Sample data for dashboard stats
    document.getElementById("totalOpportunities").innerText = "5";
    document.getElementById("totalApplications").innerText = "120";
    document.getElementById("pendingApplications").innerText = "30";

    const applicationsTable = document.getElementById("recentApplicationsTable");

    // Function to update application status
    function updateApplicationStatus(button, status) {
        const row = button.closest("tr");
        const statusCell = row.children[2].querySelector("span");
        
        if (status === "Accepted") {
            statusCell.className = "badge bg-success";
            statusCell.innerText = "Accepted";
        } else if (status === "Rejected") {
            statusCell.className = "badge bg-danger";
            statusCell.innerText = "Rejected";
        }
    }

    // Add event listeners to Accept and Reject buttons dynamically
    applicationsTable.addEventListener("click", (event) => {
        if (event.target.classList.contains("btn-outline-success")) {
            updateApplicationStatus(event.target, "Accepted");
        } else if (event.target.classList.contains("btn-outline-danger")) {
            updateApplicationStatus(event.target, "Rejected");
        }
    });
});
