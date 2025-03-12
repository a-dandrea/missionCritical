document.getElementById('goal').addEventListener('change', function() {
   let workoutType = this.value;
   let dynamicFields = document.getElementById('dynamic-fields');
   
   // Clear out any existing dynamic fields before updating
   dynamicFields.innerHTML = '';

   if (goal === "0") {
       dynamicFields.innerHTML = `
       `;
   } else if (goal === "1") {
       dynamicFields.innerHTML = `
           
       `;
   } else if (goal === "2") {
         dynamicFields.innerHTML = `
            
         `;
   } else if (goal === "3") {
         dynamicFields.innerHTML = `
            
         `;
   }
});

// Form submission handling with AJAX (same as before)
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
       document.getElementById("update-goal-form").reset();  // Reset the form
   })
   .catch(error => console.error('Error:', error));  // Log errors
});