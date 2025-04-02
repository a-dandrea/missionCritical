document.getElementById("habit-form").addEventListener("submit", function(event) {
   event.preventDefault();  // Prevent form from submitting normally

   let formData = new FormData(this);

   fetch('../backend/submit_journal.php', {  // Submit form data to backend PHP
       method: 'POST',
       body: formData
   })
   .then(response => response.json())
   .then(data => {
       alert(data.message);  // Show success/error message
       if (data.message === "Journal submitted successfully!") {
      }
       document.getElementById("habit-form").reset();  // Reset the form
   })
   .catch(error => console.error('Error:', error));  // Log errors
});