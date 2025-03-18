// opportunities-script.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Opportunities Page Loaded");

    const searchOpportunities = document.getElementById("searchOpportunities");
    const opportunityTableBody = document.getElementById("opportunityTableBody");
    const addOpportunityButton = document.getElementById("addOpportunity");

    // Function to search opportunities
    function searchOpportunitiesFunction() {
        const searchText = searchOpportunities.value.toLowerCase();
        const rows = opportunityTableBody.querySelectorAll("tr");

        rows.forEach(row => {
            const company = row.children[0].innerText.toLowerCase();
            const title = row.children[1].innerText.toLowerCase();
            const location = row.children[2].innerText.toLowerCase();
            
            if (company.includes(searchText) || title.includes(searchText) || location.includes(searchText)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // Function to add a new opportunity
    function addOpportunity() {
        const company = prompt("Enter company name:");
        const title = prompt("Enter opportunity title:");
        const location = prompt("Enter location:");
        const deadline = prompt("Enter application deadline:");
        const slots = prompt("Enter available slots:");
        
        if (company && title && location && deadline && slots) {
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td>${company}</td>
                <td>${title}</td>
                <td>${location}</td>
                <td>${deadline}</td>
                <td>${slots}</td>
                <td><span class="badge bg-success">Open</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-warning" onclick="editOpportunity(this)">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteOpportunity(this)">Delete</button>
                </td>
            `;
            opportunityTableBody.appendChild(newRow);
        } else {
            alert("Invalid input. Opportunity not added.");
        }
    }

    // Function to edit an opportunity
    window.editOpportunity = function (button) {
        const row = button.closest("tr");
        const company = prompt("Edit company name:", row.children[0].innerText);
        const title = prompt("Edit opportunity title:", row.children[1].innerText);
        const location = prompt("Edit location:", row.children[2].innerText);
        const deadline = prompt("Edit deadline:", row.children[3].innerText);
        const slots = prompt("Edit available slots:", row.children[4].innerText);

        if (company && title && location && deadline && slots) {
            row.children[0].innerText = company;
            row.children[1].innerText = title;
            row.children[2].innerText = location;
            row.children[3].innerText = deadline;
            row.children[4].innerText = slots;
        } else {
            alert("Edit cancelled or invalid input.");
        }
    }

    // Function to delete an opportunity
    window.deleteOpportunity = function (button) {
        const row = button.closest("tr");
        if (confirm("Are you sure you want to delete this opportunity?")) {
            row.remove();
        }
    }

    // Event Listeners
    searchOpportunities.addEventListener("keyup", searchOpportunitiesFunction);
    addOpportunityButton.addEventListener("click", addOpportunity);
});
