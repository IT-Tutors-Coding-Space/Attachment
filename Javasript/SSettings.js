// student-settings.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Student Settings Page Loaded");

    const saveProfileBtn = document.getElementById("saveProfile");
    const studentName = document.getElementById("studentName");
    const studentEmail = document.getElementById("studentEmail");
    const studentPassword = document.getElementById("studentPassword");
    const enable2FA = document.getElementById("enable2FA");
    const sessionTimeout = document.getElementById("sessionTimeout");
    const logoutAllBtn = document.getElementById("logoutAll");
    const saveNotificationsBtn = document.getElementById("saveNotifications");
    const emailNotifications = document.getElementById("emailNotifications");
    const smsNotifications = document.getElementById("smsNotifications");

    // Save Profile Changes
    saveProfileBtn.addEventListener("click", () => {
        const newName = studentName.value.trim();
        const newEmail = studentEmail.value.trim();
        const newPassword = studentPassword.value.trim();
        
        if (newName === "" || newEmail === "") {
            alert("⚠️ Name and Email cannot be empty!");
            return;
        }

        if (!validateEmail(newEmail)) {
            alert("⚠️ Please enter a valid email address!");
            return;
        }

        if (newPassword !== "" && newPassword.length < 6) {
            alert("⚠️ Password must be at least 6 characters long!");
            return;
        }

        alert("✅ Profile settings updated successfully!");
        studentPassword.value = ""; // Clear password field after saving
    });

    // Validate Email Function
    function validateEmail(email) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailPattern.test(email);
    }

    // Toggle Two-Factor Authentication
    enable2FA.addEventListener("change", () => {
        if (enable2FA.checked) {
            alert("🔐 Two-Factor Authentication Enabled. You will receive a verification code during login.");
        } else {
            alert("🔓 Two-Factor Authentication Disabled.");
        }
    });

    // Toggle Session Timeout
    sessionTimeout.addEventListener("change", () => {
        if (sessionTimeout.checked) {
            alert("⏳ Session Timeout Enabled. You will be logged out after inactivity.");
        } else {
            alert("🔄 Session Timeout Disabled.");
        }
    });

    // Log Out from All Devices
    logoutAllBtn.addEventListener("click", () => {
        if (confirm("⚠️ Are you sure you want to log out from all devices?")) {
            alert("✅ You have been logged out from all devices.");
            window.location.href = "login.html"; // Redirect to login page
        }
    });

    // Save Notification Preferences
    saveNotificationsBtn.addEventListener("click", () => {
        const emailPref = emailNotifications.checked ? "Enabled" : "Disabled";
        const smsPref = smsNotifications.checked ? "Enabled" : "Disabled";
        alert(`📩 Notification Settings Updated:\nEmail: ${emailPref}\nSMS: ${smsPref}`);
    });
});
