// settings-script.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Settings Page Loaded");

    const adminName = document.getElementById("adminName");
    const adminEmail = document.getElementById("adminEmail");
    const adminPassword = document.getElementById("adminPassword");
    const saveAccountBtn = document.getElementById("saveAccount");
    const enable2FA = document.getElementById("enable2FA");
    const sessionTimeout = document.getElementById("sessionTimeout");
    const logoutAllBtn = document.getElementById("logoutAll");

    // Save Account Changes
    saveAccountBtn.addEventListener("click", () => {
        const newName = adminName.value.trim();
        const newEmail = adminEmail.value.trim();
        const newPassword = adminPassword.value.trim();
        
        if (newName === "" || newEmail === "") {
            alert("Name and Email cannot be empty!");
            return;
        }

        if (newPassword !== "" && newPassword.length < 6) {
            alert("Password must be at least 6 characters long!");
            return;
        }

        alert("Account settings updated successfully!");
        adminPassword.value = ""; // Clear password field after saving
    });

    // Toggle Two-Factor Authentication
    enable2FA.addEventListener("change", () => {
        if (enable2FA.checked) {
            alert("Two-Factor Authentication Enabled. You will receive a verification code during login.");
        } else {
            alert("Two-Factor Authentication Disabled.");
        }
    });

    // Toggle Session Timeout
    sessionTimeout.addEventListener("change", () => {
        if (sessionTimeout.checked) {
            alert("Session Timeout Enabled. You will be logged out after inactivity.");
        } else {
            alert("Session Timeout Disabled.");
        }
    });

    // Log Out from All Devices
    logoutAllBtn.addEventListener("click", () => {
        if (confirm("Are you sure you want to log out from all devices?")) {
            alert("You have been logged out from all devices.");
            window.location.href = "login.html"; // Redirect to login page
        }
    });
});
