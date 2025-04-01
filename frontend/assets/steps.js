document.getElementById("update-step-goal-form").addEventListener("submit", function(event) {
   event.preventDefault();  // Prevent form from submitting normally

<<<<<<< HEAD
   // Grab the step goal value from the input field
   let stepGoal = document.getElementById("daily_step_goal").value;

    // Check if the step goal is a valid number
    if (stepGoal === "" || stepGoal < 0) {
        alert("Please enter a valid step goal.");
        return;  // Stop if the value is invalid
    }

   let formData = new FormData();
   formData.append("daily_step_goal", stepGoal);
=======
   let formData = new FormData(this);
>>>>>>> e58018534397e0429e9e125cbf44d3cab7a0b81f

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
   .catch(error => console.error('Error:', error));
});