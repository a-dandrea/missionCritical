// Form submission handling with AJAX (same as before)
document.getElementById("update-info-form").addEventListener("submit", function(event) {
   event.preventDefault();  // Prevent form from submitting normally

   let formData = new FormData(this);

   fetch('../backend/update_progress.php', {  // Submit form data to backend PHP
       method: 'POST',
       body: formData
   })
   .then(response => response.json())
   .then(data => {
       alert(data.message);  // Show success/error message
       document.getElementById("update-info-form").reset();  // Reset the form
   })
   .catch(error => console.error('Error:', error));  // Log errors
});