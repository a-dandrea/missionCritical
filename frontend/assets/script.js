document.getElementById('workout-type').addEventListener('change', function() {
    let workoutType = this.value;
    let dynamicFields = document.getElementById('dynamic-fields');
    
    // Clear out any existing dynamic fields before updating
    dynamicFields.innerHTML = '';

    if (workoutType === 'cardio') {
        dynamicFields.innerHTML = `
            <label for="duration">Duration (minutes):</label>
            <input type="number" id="duration" name="duration" required>
            
            <label for="distance">Distance (mi):</label>
            <input type="number" id="distance" name="distance">
            
            <label for="calories">Calories Burned:</label>
            <input type="number" id="calories" name="calories" required>

            <label for="avgbpm">Average BPM:</label>
            <input type="number" id="avgbpm" name="avgbpm" required>

            <label for="avgpace">Average Pace:</label>
            <input type="number" id="avgpace" name="avgpace" required>
        `;
    } else if (workoutType === 'strength') {
        dynamicFields.innerHTML = `
            <label for="type">Type of Workout:</label>
            <select name="type" id="type">
                <option value="push">Push</option>
                <option value="pull">Pull</option>
                <option value="legs">Legs</option>
            </select>
            
            <label for="duration">Duration (minutes):</label>
            <input type="number" id="duration" name="duration" required>
            
            <label for="calories">Calories:</label>
            <input type="number" id="calories" name="calories" required>

            <label for="notes">Notes:</label>
            <input type="text" id="notes" name="notes">
        `;
    } else if (workoutType === 'cycling') {
        dynamicFields.innerHTML = `
            <label for="duration">Duration (minutes):</label>
            <input type="number" id="duration" name="duration" required>
            
            <label for="distance">Distance (mi):</label>
            <input type="number" id="distance" name="distance">
            
            <label for="calories">Calories Burned:</label>
            <input type="number" id="calories" name="calories" required>

            <label for="avgbpm">Average BPM:</label>
            <input type="number" id="avgbpm" name="avgbpm" required>
        `;
    }
});

// Form submission handling with AJAX (same as before)
document.getElementById("workout-form").addEventListener("submit", function(event) {
    event.preventDefault();  // Prevent form from submitting normally

    let formData = new FormData(this);

    fetch('../backend/submit_workout.php', {  // Submit form data to backend PHP
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);  // Show success/error message
        document.getElementById("workout-form").reset();  // Reset the form
    })
    .catch(error => console.error('Error:', error));  // Log errors
});
