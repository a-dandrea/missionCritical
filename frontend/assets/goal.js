document.getElementById("update-goal-form").addEventListener("submit", function(event) {
   event.preventDefault();  // Prevent form from submitting normally

   let selectedGoals = Array.from(document.getElementById("goals").selectedOptions).map(option => option.value);

   if (selectedGoals.length < 1 || selectedGoals.length > 4) {
       alert("Please select between 1 and 4 goals.");
       return;
   }

   let formData = new FormData();
   selectedGoals.forEach((goal, index) => {
       formData.append(`goals[]`, goal);  // Send goals as an array
   });

   fetch('../backend/update_goal.php', {  // Submit form data to backend PHP
       method: 'POST',
       body: formData
   })
   .then(response => response.json())
   .then(data => {
       alert(data.message);  // Show success/error message
       if (data.message === "Goals updated successfully!") {
           // Add any needed logic after a successful database update
       }
       document.getElementById("update-goal-form").reset();  // Reset the form
   })
   .catch(error => {
       console.error('Error:', error);
       alert('An error occurred while updating the goal.');
   });
});