document.addEventListener("DOMContentLoaded", function () {
    const signupForm = document.getElementById("companySignupForm");
    const companyPassword = document.getElementById("companyPassword");
    const confirmPassword = document.getElementById("confirmCompanyPassword");
    const togglePassword = document.createElement("span");
    const toggleConfirmPassword = document.createElement("span");

    // Add show/hide toggle buttons
    togglePassword.innerHTML = '<i class="fa fa-eye"></i>';
    togglePassword.style.cursor = "pointer";
    togglePassword.classList.add("input-group-text");
    companyPassword.parentNode.appendChild(togglePassword);

    toggleConfirmPassword.innerHTML = '<i class="fa fa-eye"></i>';
    toggleConfirmPassword.style.cursor = "pointer";
    toggleConfirmPassword.classList.add("input-group-text");
    confirmPassword.parentNode.appendChild(toggleConfirmPassword);

    // Toggle password visibility
    togglePassword.addEventListener("click", function () {
        if (companyPassword.type === "password") {
            companyPassword.type = "text";
            togglePassword.innerHTML = '<i class="fa fa-eye-slash"></i>';
        } else {
            companyPassword.type = "password";
            togglePassword.innerHTML = '<i class="fa fa-eye"></i>';
        }
    });

    toggleConfirmPassword.addEventListener("click", function () {
        if (confirmPassword.type === "password") {
            confirmPassword.type = "text";
            toggleConfirmPassword.innerHTML = '<i class="fa fa-eye-slash"></i>';
        } else {
            confirmPassword.type = "password";
            toggleConfirmPassword.innerHTML = '<i class="fa fa-eye"></i>';
        }
    });
});
