document.getElementById('goal').addEventListener('change', function() {
   let workoutType = this.value;
   let dynamicFields = document.getElementById('dynamic-fields');
   
   // Clear out any existing dynamic fields before updating
   dynamicFields.innerHTML = '';

   if (goal === "No specific goal") {
       dynamicFields.innerHTML = `
       `;
   } else if (goal === "Maintain Weight") {
       dynamicFields.innerHTML = `
           <label for="weight">Current Weight (lbs):</label>
       `;
   } else if (goal === "Lose Weight") {
         dynamicFields.innerHTML = `
            <label for="weight">Goal Weight (lbs):</label>
         `;
   } else if (goal == "Increase Muscle Mass") {
         dynamicFields.innerHTML = `
            <label for="muscleMass">Goal Muscle Mass:</label>
         `;
   } else if (goal == "Increase Stamina") {
         dynamicFields.innerHTML = `
            <label for="stamina">Goal Stamina:</label>
         `;
   }
});

// Form submission handling with AJAX (same as before)
document.getElementById("goal-update-form").addEventListener("submit", function(event) {
   event.preventDefault();  // Prevent form from submitting normally

   let formData = new FormData(this);

   fetch('../backend/update_goal.php', {  // Submit form data to backend PHP
       method: 'POST',
       body: formData
   })
   .then(response => response.json())
   .then(data => {
       alert(data.message);  // Show success/error message
       document.getElementById("goal-update-form").reset();  // Reset the form
   })
   .catch(error => console.error('Error:', error));  // Log errors
});