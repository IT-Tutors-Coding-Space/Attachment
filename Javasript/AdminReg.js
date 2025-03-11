document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("adminForm").addEventListener("submit", function (event) {
        if (!validateForm()) {
            event.preventDefault();
        }
    });
});

function validateForm() {
    let fullName = document.getElementById("fullName").value.trim();
    let email = document.getElementById("email").value.trim();
    let username = document.getElementById("username").value.trim();
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;

    // Validate full name (at least 2 words)
    if (!/^[a-zA-Z ]{3,}$/.test(fullName)) {
        alert("Full Name must be at least 3 characters long and contain only letters.");
        return false;
    }

    // Validate email format
    let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address.");
        return false;
    }

    // Validate username (at least 4 characters)
    if (username.length < 4) {
        alert("Username must be at least 4 characters long.");
        return false;
    }

    // Validate password (at least 6 characters)
    if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        return false;
    }

    // Check if passwords match
    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return false;
    }

    return true;
}
