// users-script.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Users Page Loaded");

    const roleFilter = document.getElementById("roleFilter");
    const searchUsers = document.getElementById("searchUsers");
    const userTableBody = document.getElementById("userTableBody");
    const addUserButton = document.querySelector(".btn-primary");

    // Function to filter users by role
    function filterUsers() {
        const selectedRole = roleFilter.value.toLowerCase();
        const rows = userTableBody.querySelectorAll("tr");
        
        rows.forEach(row => {
            const role = row.getAttribute("data-role").toLowerCase();
            if (selectedRole === "all" || role === selectedRole) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // Function to search users
    function searchUsersFunction() {
        const searchText = searchUsers.value.toLowerCase();
        const rows = userTableBody.querySelectorAll("tr");

        rows.forEach(row => {
            const name = row.children[1].innerText.toLowerCase();
            const email = row.children[2].innerText.toLowerCase();
            
            if (name.includes(searchText) || email.includes(searchText)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // Function to add a new user
    function addUser() {
        const name = prompt("Enter user name:");
        const email = prompt("Enter user email:");
        const role = prompt("Enter user role (student/company/admin):").toLowerCase();

        if (name && email && (role === "student" || role === "company" || role === "admin")) {
            const newRow = document.createElement("tr");
            newRow.setAttribute("data-role", role);
            newRow.innerHTML = `
                <td>NEW</td>
                <td>${name}</td>
                <td>${email}</td>
                <td>${role.charAt(0).toUpperCase() + role.slice(1)}</td>
                <td><span class="badge bg-success">Active</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-warning" onclick="editUser(this)">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(this)">Delete</button>
                </td>
            `;
            userTableBody.appendChild(newRow);
        } else {
            alert("Invalid input. User not added.");
        }
    }

    // Function to edit a user
    window.editUser = function (button) {
        const row = button.closest("tr");
        const name = prompt("Edit name:", row.children[1].innerText);
        const email = prompt("Edit email:", row.children[2].innerText);

        if (name && email) {
            row.children[1].innerText = name;
            row.children[2].innerText = email;
        } else {
            alert("Edit cancelled or invalid input.");
        }
    }

    // Function to delete a user
    window.deleteUser = function (button) {
        const row = button.closest("tr");
        if (confirm("Are you sure you want to delete this user?")) {
            row.remove();
        }
    }

    // Event Listeners
    roleFilter.addEventListener("change", filterUsers);
    searchUsers.addEventListener("keyup", searchUsersFunction);
    addUserButton.addEventListener("click", addUser);
});
