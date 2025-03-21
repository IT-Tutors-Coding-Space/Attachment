document.addEventListener("DOMContentLoaded", function () {
    const signupForm = document.getElementById("adminSignupForm");
    const adminEmail = document.getElementById("adminEmail");
    const adminPassword = document.getElementById("adminPassword");
    const confirmPassword = document.getElementById("confirmAdminPassword");
    const adminRole = document.getElementById("adminRole");
    const togglePasswordIcons = document.querySelectorAll(".toggle-password");

    // Real-time validation messages
    const emailError = document.createElement("small");
    emailError.classList.add("text-danger");
    adminEmail.parentNode.appendChild(emailError);

    const passwordError = document.createElement("small");
    passwordError.classList.add("text-danger");
    adminPassword.parentNode.appendChild(passwordError);

    const confirmPasswordError = document.createElement("small");
    confirmPasswordError.classList.add("text-danger");
    confirmPassword.parentNode.appendChild(confirmPasswordError);

    adminEmail.addEventListener("input", function () {
        const emailPattern = /^[a-zA-Z0-9._%+-]+@attachme\.admin$/;
        if (!emailPattern.test(adminEmail.value)) {
            emailError.textContent = "Invalid email format! Use format: username@attachme.admin";
        } else {
            emailError.textContent = "";
        }
    });

    adminPassword.addEventListener("input", function () {
        let errors = [];
        if (adminPassword.value.length < 8) {
            errors.push("At least 8 characters");
        }
        if (!/[A-Z]/.test(adminPassword.value)) {
            errors.push("One uppercase letter");
        }
        if (!/[0-9]/.test(adminPassword.value)) {
            errors.push("One number");
        }
        if (!/[!@#$%^&*]/.test(adminPassword.value)) {
            errors.push("One special character (!@#$%^&*)");
        }
        passwordError.textContent = errors.length > 0 ? errors.join(", ") : "";
    });

    confirmPassword.addEventListener("input", function () {
        if (adminPassword.value !== confirmPassword.value) {
            confirmPasswordError.textContent = "Passwords do not match";
        } else {
            confirmPasswordError.textContent = "";
        }
    });

    // Password show/hide toggle
    togglePasswordIcons.forEach(icon => {
        icon.addEventListener("click", function () {
            const passwordField = this.previousElementSibling;
            if (passwordField.type === "password") {
                passwordField.type = "text";
                this.innerHTML = '<i class="fa fa-eye-slash"></i>';
            } else {
                passwordField.type = "password";
                this.innerHTML = '<i class="fa fa-eye"></i>';
            }
        });
    });

    signupForm.addEventListener("submit", function (event) {
        if (emailError.textContent || passwordError.textContent || confirmPasswordError.textContent) {
            event.preventDefault();
        } else {
            event.preventDefault(); // Prevent form submission for demo purposes
            alert("Registration successful! Redirecting to login page...");
            window.location.href = "login.html";
        }
    });
});
