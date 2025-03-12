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
           
       `;
   } else if (goal === "Lose Weight") {
         dynamicFields.innerHTML = `
            
         `;
   } else if (goal == "Increase Muscle Mass") {
         dynamicFields.innerHTML = `
            
         `;
   } else if (goal == "Increase Stamina") {
         dynamicFields.innerHTML = `
            
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