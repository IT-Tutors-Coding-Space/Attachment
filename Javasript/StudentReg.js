document.addEventListener("DOMContentLoaded", function () {
    const signupForm = document.getElementById("studentSignupForm");
    const studentEmail = document.getElementById("studentEmail");
    const studentPassword = document.getElementById("studentPassword");
    const confirmPassword = document.getElementById("confirmPassword");

    const emailError = document.createElement("small");
    emailError.classList.add("text-danger");
    studentEmail.parentNode.appendChild(emailError);

    const passwordError = document.createElement("small");
    passwordError.classList.add("text-danger");
    studentPassword.parentNode.appendChild(passwordError);

    const confirmPasswordError = document.createElement("small");
    confirmPasswordError.classList.add("text-danger");
    confirmPassword.parentNode.appendChild(confirmPasswordError);

    studentEmail.addEventListener("input", function () {
        const emailPattern = /^[a-zA-Z0-9._%+-]+@attachme\.student$/;
        if (!emailPattern.test(studentEmail.value)) {
            emailError.textContent = "Invalid email format! Use format: username@attachme.student";
        } else {
            emailError.textContent = "";
        }
    });

    studentPassword.addEventListener("input", function () {
        let errors = [];
        if (studentPassword.value.length < 8) {
            errors.push("At least 8 characters");
        }
        if (!/[A-Z]/.test(studentPassword.value)) {
            errors.push("One uppercase letter");
        }
        if (!/[0-9]/.test(studentPassword.value)) {
            errors.push("One number");
        }
        if (!/[!@#$%^&*]/.test(studentPassword.value)) {
            errors.push("One special character (!@#$%^&*)");
        }
        passwordError.textContent = errors.length > 0 ? errors.join(", ") : "";
    });

    confirmPassword.addEventListener("input", function () {
        if (studentPassword.value !== confirmPassword.value) {
            confirmPasswordError.textContent = "Passwords do not match";
        } else {
            confirmPasswordError.textContent = "";
        }
    });

    signupForm.addEventListener("submit", function (event) {
        if (emailError.textContent || passwordError.textContent || confirmPasswordError.textContent) {
            event.preventDefault();
        } else {
            event.preventDefault(); // Prevent form submission for demo purposes
            alert("Registration successful! Redirecting to login page...");
            window.location.href = "login.html"; // Redirect to login page
        }
    });
});
