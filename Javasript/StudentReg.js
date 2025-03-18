document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("registrationForm");

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    let isValid = true;

    // Name validation
    const namePattern = /^[A-Za-z]{2,50}$/;
    const firstName = document.getElementById('first_name').value;
    const lastName = document.getElementById('last_name').value;
    if (!namePattern.test(firstName)) {
        isValid = false;
        document.getElementById('first_name_error').textContent = 'First name should be 2-50 characters long and contain only letters.';
    } else {
        document.getElementById('first_name_error').textContent = '';
    }
    if (!namePattern.test(lastName)) {
        isValid = false;
        document.getElementById('last_name_error').textContent = 'Last name should be 2-50 characters long and contain only letters.';
    } else {
        document.getElementById('last_name_error').textContent = '';
    }

    // Email validation
    const email = document.getElementById('email').value;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        isValid = false;
        document.getElementById('email_error').textContent = 'Please enter a valid email address.';
    } else {
        document.getElementById('email_error').textContent = '';
    }

    // Password validation
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    if (!passwordPattern.test(password)) {
        isValid = false;
        document.getElementById('password_error').textContent = 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.';
    } else {
        document.getElementById('password_error').textContent = '';
    }
    if (password !== confirmPassword) {
        isValid = false;
        document.getElementById('confirm_password_error').textContent = 'Passwords do not match.';
    } else {
        document.getElementById('confirm_password_error').textContent = '';
    }

    if (isValid) {
        this.submit();
    }
  });
});
