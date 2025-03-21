// Update goal stuff
document.getElementById("update-goal-form").addEventListener("submit", function(event) {
   event.preventDefault();  // Prevent form from submitting normally

   let formData = new FormData(this);

   fetch('../backend/update_goal.php', {  // Submit form data to backend PHP
       method: 'POST',
       body: formData
   })
      .then(response => response.json())
      .then(data => {
         alert(data.message);  // Show success/error message
         if(data.message === "Goal updated successfully!"){
            //add any needed logic after a successful database update.
         }
         document.getElementById("update-goal-form").reset();  // Reset the form
      })
      .catch(error => {
         console.error('Error:', error);
         alert('An error occurred while updating the goal.');
      });
});