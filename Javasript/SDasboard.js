// script.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Dashboard loaded!");

    // Dynamic content update
    const submittedCount = document.getElementById("submittedCount");
    const acceptedCount = document.getElementById("acceptedCount");
    const pendingCount = document.getElementById("pendingCount");
    const dateTimeElement = document.getElementById("dateTime");
    const deadlineTimer = document.getElementById("deadlineTimer");

    // Simulate fetching data from an API
    setTimeout(() => {
        submittedCount.innerText = 5;
        acceptedCount.innerText = 2;
        pendingCount.innerText = 3;
    }, 2000);

    // Show current date and time
    function updateDateTime() {
        const now = new Date();
        dateTimeElement.innerText = `Today is ${now.toDateString()}, ${now.toLocaleTimeString()}`;
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    // Clear notifications
    window.clearNotifications = function () {
        const notificationList = document.getElementById("notificationList");
        notificationList.innerHTML = "<li>No new notifications</li>";
    }

    // Countdown timer for deadlines
    function startCountdown(days) {
        let remainingDays = days;
        setInterval(() => {
            if (remainingDays > 0) {
                remainingDays--;
                deadlineTimer.innerText = `${remainingDays} days`;
            } else {
                deadlineTimer.innerText = "Deadline reached";
            }
        }, 86400000); // Update every 24 hours
    }
    startCountdown(3);
});
