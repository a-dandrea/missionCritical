// Update activity level stuff
document.getElementById("update-activity-form").addEventListener("submit", function(event) {
   event.preventDefault();  // Prevent form from submitting normally

   let formData = new FormData(this);

   fetch('../backend/update_activity.php', {  // Submit form data to backend PHP
       method: 'POST',
       body: formData
   })
   .then(response => response.json())
   .then(data => {
       alert(data.message);  // Show success/error message
       if(data.message === "Activity level updated successfully!"){
         //add any needed logic after a successful database update.
       }
       document.getElementById("update-activity-form").reset();  // Reset the form
   })
   .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while updating the activity level.');
  });
});