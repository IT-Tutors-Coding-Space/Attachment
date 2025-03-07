document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registrationForm");

    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent form submission

        // Get form values
        const companyName = document.getElementById("companyName").value.trim();
        const email = document.getElementById("email").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const website = document.getElementById("website").value.trim();
        const address = document.getElementById("address").value.trim();
         
        const password = document.getElementById("password").value.trim();
        const confirmPassword = document.getElementById("confirmPassword").value.trim();

        // Email validation
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            return;
        }

        // Phone number validation (10 digits)
        const phonePattern = /^[0-9]{10}$/;
        if (!phonePattern.test(phone)) {
            alert("Phone number must be 10 digits.");
            return;
        }

        // Password matching validation
        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return;
        }

        // Check required fields
        if (!companyName || !email || !phone || !industry || !address || !description || !password || !confirmPassword) {
            alert("Please fill in all required fields.");
            return;
        }

        // Simulating form submission (Replace with actual backend request)
        alert("Registration successful!");
        form.reset();
    });
});
