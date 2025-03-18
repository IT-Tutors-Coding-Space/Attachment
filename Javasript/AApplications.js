// applications-script.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Applications Page Loaded");

    const searchApplications = document.getElementById("searchApplications");
    const applicationsTableBody = document.getElementById("applicationsTableBody");

    // Function to search applications
    function searchApplicationsFunction() {
        const searchText = searchApplications.value.toLowerCase();
        const rows = applicationsTableBody.querySelectorAll("tr");

        rows.forEach(row => {
            const student = row.children[0].innerText.toLowerCase();
            const opportunity = row.children[1].innerText.toLowerCase();
            const company = row.children[2].innerText.toLowerCase();
            
            if (student.includes(searchText) || opportunity.includes(searchText) || company.includes(searchText)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // Function to update application status
    function updateApplicationStatus(button, status) {
        const row = button.closest("tr");
        const statusCell = row.children[4].querySelector("span");
        
        if (status === "Accepted") {
            statusCell.className = "badge bg-success";
        } else if (status === "Rejected") {
            statusCell.className = "badge bg-danger";
        }
        
        statusCell.innerText = status;
    }

    // Add event listeners to Accept and Reject buttons dynamically
    applicationsTableBody.addEventListener("click", (event) => {
        if (event.target.classList.contains("btn-outline-success")) {
            updateApplicationStatus(event.target, "Accepted");
        } else if (event.target.classList.contains("btn-outline-danger")) {
            updateApplicationStatus(event.target, "Rejected");
        }
    });

    // Event Listener for search input
    searchApplications.addEventListener("keyup", searchApplicationsFunction);
});
