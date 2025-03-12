document.getElementById('submission-form').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevents the default form submission behavior
  
    // Form validation (checking if the consent box is checked)
    const consent = document.getElementById('consent');
    if (!consent.checked) {
      alert('You must agree to the terms and conditions.');
      return;
    }
  
    // If the form is valid, display a confirmation message
    const confirmationMessage = document.getElementById('confirmation-message');
    confirmationMessage.textContent = 'Your submission has been received! We will contact you soon.';
    
    // Optionally, clear the form fields after submission
    document.getElementById('submission-form').reset();
  });
  