// contact-support.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Contact Support Page Loaded");

    const contactForm = document.getElementById("contactForm");
    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const subjectInput = document.getElementById("subject");
    const messageInput = document.getElementById("message");

    // Email validation function
    function validateEmail(email) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailPattern.test(email);
    }

    // Function to display a feedback message
    function showFeedback(message, type = "success") {
        const feedbackDiv = document.createElement("div");
        feedbackDiv.className = `alert alert-${type === "success" ? "success" : "danger"} mt-3`;
        feedbackDiv.innerText = message;
        contactForm.appendChild(feedbackDiv);

        setTimeout(() => {
            feedbackDiv.remove();
        }, 3000);
    }

    // Form submission event
    contactForm.addEventListener("submit", (event) => {
        event.preventDefault();

        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const subject = subjectInput.value.trim();
        const message = messageInput.value.trim();

        if (name === "" || email === "" || subject === "" || message === "") {
            showFeedback("⚠️ All fields are required. Please fill out the form completely.", "danger");
            return;
        }

        if (!validateEmail(email)) {
            showFeedback("⚠️ Please enter a valid email address.", "danger");
            return;
        }

        showFeedback("✅ Your message has been sent successfully! Our support team will get back to you soon.", "success");
        contactForm.reset();
    });
});
