<?php
header("Content-Type: application/json");  // Send JSON response

// Include the database connection file
include('config.php');

// Retrieve the form data from the POST request
$workoutType = $_POST['workout-type'] ?? null;
$duration = $_POST['duration'] ?? null;
$distance = $_POST['distance'] ?? null;
$calories = $_POST['calories'] ?? null;
$avgbpm = $_POST['avgbpm'] ?? null;
$avgpace = $_POST['avgpace'] ?? null;
$type = $_POST['type'] ?? null;
$notes = $_POST['notes'] ?? null;

// Prepare SQL query based on the workout type

if ($workoutType == 'cardio') {
    // Cardio-specific columns (duration, distance, calories, avg BPM, avg pace)
    $sql = "INSERT INTO workouts (exercise, duration, distance, calories, avgbpm, avgpace, workout_type) 
            VALUES ('Cardio', '$duration', '$distance', '$calories', '$avgbpm', '$avgpace', '$workoutType')";
} elseif ($workoutType == 'strength') {
    // Strength-specific columns (type of workout, duration, calories, notes)
    $sql = "INSERT INTO workouts (exercise, duration, calories, notes, workout_type) 
            VALUES ('Strength', '$duration', '$calories', '$notes', '$workoutType')";
} elseif ($workoutType == 'cycling') {
    // Cycling-specific columns (duration, distance, calories, avg BPM)
    $sql = "INSERT INTO workouts (exercise, duration, distance, calories, avgbpm, avgpace, workout_type) 
            VALUES ('Cycling', '$duration', '$distance', '$calories', '$avgbpm', '$avgpace' '$workoutType')";
} else {
    echo json_encode(["message" => "Invalid workout type"]);
    exit();
}

// Insert data into MySQL
if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Workout logged successfully!"]);
} else {
    echo json_encode(["message" => "Error: " . $conn->error]);
}

$conn->close();
?>
