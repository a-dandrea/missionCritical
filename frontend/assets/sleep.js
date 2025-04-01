document.getElementById("update-sleep-goal-form").addEventListener("submit", function(event) {
    event.preventDefault();  // Prevent form from submitting normally

    // Grab the sleep goal value from the input field
    let sleepGoal = document.getElementById("daily_outside_goal").value;
 
     // Check if the sleep goal is a valid number
     if (sleepGoal === "" || sleepGoal < 0) {
         alert("Please enter a valid goal for sleep time.");
         return;  // Stop if the value is invalid
     }
 
    let formData = new FormData();
    formData.append("daily_sleep_goal", sleepGoal);
 
    fetch('../backend/update_sleep.php', {  // Submit form data to backend PHP
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);  // Show success/error message
        if (data.message === "Sleep goal updated successfully!") {
        }
        document.getElementById("update-sleep-goal-form").reset();  // Reset the form
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the goal.');
    });
 });