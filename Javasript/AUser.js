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
            const email = row.children[1].innerText.toLowerCase();
            
            if (email.includes(searchText)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // Function to edit a user
    window.editUser = function (button) {
        const row = button.closest("tr");
        const userId = row.children[0].innerText; // Assuming the first cell contains the user ID
        // Redirect to edit user page
        window.location.href = `editUser.php?user_id=${userId}`; 
    }

    // Function to delete a user
    window.deleteUser = function (button) {
        const row = button.closest("tr");
        const userId = row.children[0].innerText; // Assuming the first cell contains the user ID

        if (confirm("Are you sure you want to delete this user?")) {
            // Send AJAX request to deleteUser.php
            fetch('Pages/Admin/deleteUser.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'user_id': userId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('User deleted successfully!');
                    row.remove(); // Remove the row from the table
                } else {
                    alert('Failed to delete user: ' + data.message);
                }
            });
        }
    }

    // Event Listeners
    roleFilter.addEventListener("change", filterUsers);
    searchUsers.addEventListener("keyup", searchUsersFunction);
});
