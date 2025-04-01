document.getElementById("update-active-goal-form").addEventListener("submit", function(event) {
    event.preventDefault();  // Prevent form from submitting normally

    // Grab the active goal value from the input field
    let activeGoal = document.getElementById("daily_active_goal").value;
 
     // Check if the active goal is a valid number
     if (activeGoal === "" || activeGoal < 0) {
         alert("Please enter a valid step goal.");
         return;  // Stop if the value is invalid
     }
 
    let formData = new FormData();
    formData.append("daily_active_goal", activeGoal);
 
    fetch('../backend/update_active.php', {  // Submit form data to backend PHP
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);  // Show success/error message
        if (data.message === "Active Minutes goal updated successfully!") {
        }
        document.getElementById("update-active-goal-form").reset();  // Reset the form
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the goal.');
    });
 });