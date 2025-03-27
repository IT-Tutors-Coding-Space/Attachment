// admin-script.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Admin Dashboard Loaded!");

    // Fetch and update dashboard stats dynamically
    const stats = {
        totalUsers: 1200,
        totalCompanies: 350,
        totalOpportunities: 580,
        totalApplications: 240,
    };

    document.getElementById("totalUsers").innerText = stats.totalUsers;
    document.getElementById("totalCompanies").innerText = stats.totalCompanies;
    document.getElementById("totalOpportunities").innerText = stats.totalOpportunities;
    document.getElementById("totalApplications").innerText = stats.totalApplications;

    // Notifications dropdown toggle
    const notificationButton = document.querySelector(".btn-primary");
    notificationButton.addEventListener("click", () => {
        alert("No new notifications at the moment.");
    });
});
