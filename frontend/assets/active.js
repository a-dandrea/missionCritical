document.getElementById("update-active-goal-form").addEventListener("submit", function(event) {
    event.preventDefault();  // Prevent form from submitting normally
 
    let formData = new FormData();
 
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