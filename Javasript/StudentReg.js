function toggleForm(formType) {
    console.log('toggleForm called with formType:', formType);
    if (formType === "login") {
        document.getElementById("loginForm").style.display = "block";
        document.getElementById("registerForm").style.display = "none";
        document.getElementById("welcomeMessage").style.display = "none";
        document.getElementById("resetPasswordForm").style.display = "none";
    } else if (formType === "register") {
        document.getElementById("loginForm").style.display = "none";
        document.getElementById("registerForm").style.display = "block";
        document.getElementById("welcomeMessage").style.display = "none";
        document.getElementById("resetPasswordForm").style.display = "none";
    } else if (formType === "resetPassword") {
        document.getElementById("loginForm").style.display = "none";
        document.getElementById("registerForm").style.display = "none";
        document.getElementById("welcomeMessage").style.display = "none";
        document.getElementById("resetPasswordForm").style.display = "block";
    }
}

function validateLogin() {
    let username = document.getElementById("loginUsername").value;
    let password = document.getElementById("loginPassword").value;

    // Dummy validation for demonstration
    if (username === "user@example.com" && password === "password") {
        document.getElementById("loginForm").style.display = "none";
        document.getElementById("welcomeMessage").style.display = "block";
        return false; // Prevent form submission
    } else {
        alert("Invalid details. Please check your username and password.");
        return false; // Prevent form submission
    }
}

function validateRegistration() {
    let password = document.getElementById("registerPassword").value;
    let confirmPassword = document.getElementById("confirmPassword").value;

    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return false; // Prevent form submission
    }

    let firstName = document.getElementById("registerFirstName").value;
    let lastName = document.getElementById("registerLastName").value;
    let email = document.getElementById("registerEmail").value;
    let gender = document.querySelector('input[name="gender"]:checked').value;

    // Dummy registration success message
    alert(
        "Registration successful!\n" +
        "Name: " +
        firstName +
        " " +
        lastName +
        "\n" +
        "Email: " +
        email +
        "\n" +
        "Gender: " +
        gender
    );
    toggleForm("login"); // Redirect to login form
    return false; // Prevent form submission
}

function validateResetPassword() {
    let email = document.getElementById("resetEmail").value;
    let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

    if (!emailPattern.test(email)) {
        document.getElementById("emailError").style.display = "block";
        return false;
    } else {
        document.getElementById("emailError").style.display = "none";
        // Simulate sending a reset code via email
        alert("A reset code (12345) has been sent to your email address.");
        // Proceed to ask for the reset code
        promptForResetCode();
        return false;
    }
}

function promptForResetCode() {
    let code = prompt("Enter the reset code sent to your email:");

    if (code === "12345") {
        let newPassword = prompt("Enter your new password:");

        if (newPassword) {
            alert("Your password has been reset successfully.");
            toggleForm("login"); // Redirect to login form
        } else {
            alert("Please enter a new password.");
        }
    } else {
        alert("Invalid code. Please try again.");
    }
}