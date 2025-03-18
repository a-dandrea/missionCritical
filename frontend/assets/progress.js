// Update goal stuff
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
       if(data.message === "Progress added successfully!"){
         //add any needed logic after a successful database update.
       }
       document.getElementById("update-info-form").reset();  // Reset the form
   })
   .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while updating the goal.');
  });
});