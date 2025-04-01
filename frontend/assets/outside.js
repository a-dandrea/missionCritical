document.getElementById("update-outside-goal-form").addEventListener("submit", function(event) {
    event.preventDefault();  // Prevent form from submitting normally

    // Grab the active goal value from the input field
    let outsideGoal = document.getElementById("daily_outside_goal").value;
 
     // Check if the active goal is a valid number
     if (outsideGoal === "" || outsideGoal < 0) {
         alert("Please enter a valid goal for time spent outside.");
         return;  // Stop if the value is invalid
     }
 
    let formData = new FormData();
    formData.append("daily_outside_goal", outsideGoal);
 
    fetch('../backend/update_outside.php', {  // Submit form data to backend PHP
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);  // Show success/error message
        if (data.message === "Outside Time goal updated successfully!") {
        }
        document.getElementById("update-outside-goal-form").reset();  // Reset the form
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the goal.');
    });
 });