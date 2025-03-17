document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registrationForm");
  
    form.addEventListener("submit", function (event) {
      let isValid = true;
      let errorMessage = "";
  
      // Get form values
      const firstName = document.getElementById("first_name").value.trim();
      const lastName = document.getElementById("last_name").value.trim();
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value.trim();
      const confirmPassword = document
        .getElementById("confirm_password")
        .value.trim();
      const gender = document.querySelector('input[name="gender"]:checked');
      const yearOfStudy = document.getElementById("year_of_study").value;
      const course = document.getElementById("course").value;
  
      // Validate First Name & Last Name
      if (firstName === "" || lastName === "") {
        isValid = false;
        errorMessage += "First Name and Last Name are required.\n";
      }
  
      // Validate Email
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email.match(emailPattern)) {
        isValid = false;
        errorMessage += "Enter a valid email address.\n";
      }
  
      // Validate Password
      if (password.length < 6) {
        isValid = false;
        errorMessage += "Password must be at least 6 characters long.\n";
      }
      if (password !== confirmPassword) {
        isValid = false;
        errorMessage += "Passwords do not match.\n";
      }
  
      // Validate Gender Selection
      if (!gender) {
        isValid = false;
        errorMessage += "Please select a gender.\n";
      }
  
      // Validate Year of Study & Course
      if (yearOfStudy === "") {
        isValid = false;
        errorMessage += "Please select a Year of Study.\n";
      }
  
      if (course === "") {
        isValid = false;
        errorMessage += "Please select a Course.\n";
      }
  
      // Show errors and prevent form submission if invalid
      if (!isValid) {
        alert(errorMessage);
        event.preventDefault(); // Stop form submission
      }
    });
  });
  