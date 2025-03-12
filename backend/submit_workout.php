<?php
header("Content-Type: application/json");  // Send JSON response

include('config.php');  // Database connection

// Retrieve form data
$workoutType = $_POST['workout-type'];
$duration = $_POST['duration'] ?? null;
$distance = $_POST['distance'] ?? null;
$calories = $_POST['calories'] ?? null;
$reps = $_POST['reps'] ?? null;
$sets = $_POST['sets'] ?? null;
$weight = $_POST['weight'] ?? null;

// Prepare the SQL query based on workout type
if ($workoutType == 'cardio') {
    $sql = "INSERT INTO workouts (exercise, duration, distance, calories, workout_type) 
            VALUES ('Cardio', '$duration', '$distance', '$calories', '$workoutType')";
} elseif ($workoutType == 'strength') {
    $sql = "INSERT INTO workouts (exercise, reps, sets, weight, workout_type) 
            VALUES ('Strength', '$reps', '$sets', '$weight', '$workoutType')";
} elseif ($workoutType == 'yoga') {
    $sql = "INSERT INTO workouts (exercise, duration, calories, workout_type) 
            VALUES ('Yoga', '$duration', '$calories', '$workoutType')";
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
