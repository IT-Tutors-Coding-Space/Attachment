// companies-script.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Companies Page Loaded");

    const searchCompanies = document.getElementById("searchCompanies");
    const companyTableBody = document.getElementById("companyTableBody");
    const addCompanyButton = document.querySelector(".btn-primary");

    // Function to search companies
    function searchCompaniesFunction() {
        const searchText = searchCompanies.value.toLowerCase();
        const rows = companyTableBody.querySelectorAll("tr");

        rows.forEach(row => {
            const name = row.children[1].innerText.toLowerCase();
            const email = row.children[2].innerText.toLowerCase();
            const industry = row.children[3].innerText.toLowerCase();
            const location = row.children[4].innerText.toLowerCase();
            
            if (name.includes(searchText) || email.includes(searchText) || industry.includes(searchText) || location.includes(searchText)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // Function to add a new company
    function addCompany() {
        const name = prompt("Enter company name:");
        const email = prompt("Enter company email:");
        const industry = prompt("Enter industry:");
        const location = prompt("Enter company location:");

        if (name && email && industry && location) {
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td><img src="images/default-logo.png" alt="Company Logo" class="img-thumbnail" width="50"></td>
                <td>${name}</td>
                <td>${email}</td>
                <td>${industry}</td>
                <td>${location}</td>
                <td><span class="badge bg-success">Active</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-warning" onclick="editCompany(this)">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCompany(this)">Delete</button>
                </td>
            `;
            companyTableBody.appendChild(newRow);
        } else {
            alert("Invalid input. Company not added.");
        }
    }

    // Function to edit a company
    window.editCompany = function (button) {
        const row = button.closest("tr");
        const name = prompt("Edit company name:", row.children[1].innerText);
        const email = prompt("Edit company email:", row.children[2].innerText);
        const industry = prompt("Edit industry:", row.children[3].innerText);
        const location = prompt("Edit location:", row.children[4].innerText);

        if (name && email && industry && location) {
            row.children[1].innerText = name;
            row.children[2].innerText = email;
            row.children[3].innerText = industry;
            row.children[4].innerText = location;
        } else {
            alert("Edit cancelled or invalid input.");
        }
    }

    // Function to delete a company
    window.deleteCompany = function (button) {
        const row = button.closest("tr");
        if (confirm("Are you sure you want to delete this company?")) {
            row.remove();
        }
    }

    // Event Listeners
    searchCompanies.addEventListener("keyup", searchCompaniesFunction);
    addCompanyButton.addEventListener("click", addCompany);
});
