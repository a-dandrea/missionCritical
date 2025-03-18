document.getElementById('workout-type').addEventListener('change', function() {
    let workoutType = this.value;
    let dynamicFields = document.getElementById('dynamic-fields');
    
    // Clear out any existing dynamic fields before updating
    dynamicFields.innerHTML = '';

    if (workoutType === 'Other Workout') {
        dynamicFields.innerHTML = `
            <label for="duration">Duration (minutes):</label>
            <input type="number" id="duration" name="duration" required>
            
            <label for="calories">Calories Burned:</label>
            <input type="number" id="calories" name="calories" required>

            <label for="avgbpm">Average BPM:</label>
            <input type="number" id="avgbpm" name="avgbpm" required>

            <label for="notes">Notes:</label>
            <input type="text" id="notes" name="notes">
        `;

    } else if (workoutType === 'Strength/ Weight Training') {
        dynamicFields.innerHTML = `

            <div class = "selectBox" onclick = "showCheckboxes()">
                <select>
                    <option> Select Muscle Groups </option>
                </select>
                <div class = "overSelect"></div>
            </div>

            <div id="checkBoxes" style="display: none;">
                <label><input type="checkbox" name="type" value="Chest"> Chest</label>
                <label><input type="checkbox" name="type" value="Triceps"> Triceps</label>
                <label><input type="checkbox" name="type" value="Biceps"> Biceps</label>
                <label><input type="checkbox" name="type" value="Upper Back"> Upper Back</label>
                <label><input type="checkbox" name="type" value="Lower Back"> Lower Back</label>
                <label><input type="checkbox" name="type" value="Core"> Core</label>
                <label><input type="checkbox" name="type" value="Hamstrings"> Hamstrings</label>
                <label><input type="checkbox" name="type" value="Quads"> Quads</label>
                <label><input type="checkbox" name="type" value="Glutes"> Glutes</label>
                <label><input type="checkbox" name="type" value="Calves"> Calves</label>
            </div>
            
            <label for="duration">Duration (minutes):</label>
            <input type="number" id="duration" name="duration" required>
            
            <label for="calories">Calories Burned:</label>
            <input type="number" id="calories" name="calories" required>

            <label for="avgbpm">Average BPM:</label>
            <input type="number" id="avgbpm" name="avgbpm" required>

            <label for="notes">Notes:</label>
            <input type="text" id="notes" name="notes">
        `;
    } else if (workoutType === 'Running') {
        dynamicFields.innerHTML = `
            <label for="duration">Duration (minutes):</label>
            <input type="number" id="duration" name="duration" required>

            <label for="distance">Distance (mi):</label>
            <input type="number" id="distance" name="distance">

            <label for="avgpace">Average Pace:</label>
            <input type="number" id="avgpace" name="avgpace" required>
            
            <label for="calories">Calories Burned:</label>
            <input type="number" id="calories" name="calories" required>

            <label for="avgbpm">Average BPM:</label>
            <input type="number" id="avgbpm" name="avgbpm" required>

            <label for="notes">Notes:</label>
            <input type="text" id="notes" name="notes">
        `;
    } else if (workoutType === 'Cycling') {
        dynamicFields.innerHTML = `
        <label for="duration">Duration (minutes):</label>
        <input type="number" id="duration" name="duration" required>

        <label for="distance">Distance (mi):</label>
        <input type="number" id="distance" name="distance">
        
        <label for="calories">Calories Burned:</label>
        <input type="number" id="calories" name="calories" required>

        <label for="avgbpm">Average BPM:</label>
        <input type="number" id="avgbpm" name="avgbpm" required>

        <label for="notes">Notes:</label>
        <input type="text" id="notes" name="notes">
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

// Function to toggle the checkboxes
let show = false;
function showCheckboxes() {
    let checkboxes = document.getElementById("checkBoxes");
    if (show) {
        checkboxes.style.display = "none";
    } else {
        checkboxes.style.display = "block";
    }
    show = !show;
}
