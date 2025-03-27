// analytics-script.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Analytics Page Loaded");

    // Sample data for charts
    const applicationsData = {
        labels: ["Safaricom", "KCB Group", "Equity Bank", "EABL", "KPLC"],
        datasets: [{
            label: "Applications Received",
            data: [120, 95, 80, 60, 45],
            backgroundColor: ["#4CAF50", "#2196F3", "#FFC107", "#FF5722", "#9C27B0"],
            borderWidth: 1
        }]
    };

    const statusData = {
        labels: ["Accepted", "Rejected"],
        datasets: [{
            label: "Application Status",
            data: [250, 100],
            backgroundColor: ["#4CAF50", "#FF5722"],
            borderWidth: 1
        }]
    };

    // Render Applications per Company Chart
    const applicationsChartCtx = document.getElementById("applicationsChart").getContext("2d");
    new Chart(applicationsChartCtx, {
        type: "bar",
        data: applicationsData,
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Render Accepted vs. Rejected Applications Chart
    const statusChartCtx = document.getElementById("statusChart").getContext("2d");
    new Chart(statusChartCtx, {
        type: "doughnut",
        data: statusData,
        options: {
            responsive: true
        }
    });

    // Update Overview Stats
    document.getElementById("totalApplications").innerText = "350";
    document.getElementById("acceptedApplications").innerText = "250";
    document.getElementById("rejectedApplications").innerText = "100";
    document.getElementById("activeCompanies").innerText = "15";
});
