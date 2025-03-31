document.getElementById("update-step-goal-form").addEventListener("submit", function(event) {
   event.preventDefault();  // Prevent form from submitting normally

   // Grab the step goal value from the input field
   let stepGoal = document.getElementById("daily_step_goal").value;

   let formData = new FormData();
   formData.append("daily_step_goal", stepGoal);

   fetch('../backend/update_steps.php', {  // Submit form data to backend PHP
       method: 'POST',
       body: formData
   })
   .then(response => response.json())
   .then(data => {
       alert(data.message);  // Show success/error message
       if (data.message === "Step goal updated successfully!") {
       }
       document.getElementById("update-step-goal-form").reset();  // Reset the form
   })
   .catch(error => {
       console.error('Error:', error);
       alert('An error occurred while updating the goal.');
   });
});