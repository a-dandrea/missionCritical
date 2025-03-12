<?php
header("Content-Type: application/json");  // Send JSON response

include('config.php');  // Database connection

// Retrieve form data
$goal = $_POST['goal'];
$weight = $_POST['weight'] ?? null;
$muscleMass = $_POST['muscleMass'] ?? null;
$stamina = $_POST['stamina'] ?? null;

// Prepare the SQL query based on workout type
if ($goal == "No specific goal") {
    $sql = "INSERT INTO workouts (exercise, duration, distance, calories, workout_type) 
            VALUES ('Cardio', '$duration', '$distance', '$calories', '$workoutType')";
} elseif ($goal == "Maintain Weight") {
    $sql = "INSERT INTO workouts (exercise, reps, sets, weight, workout_type) 
            VALUES ('Strength', '$reps', '$sets', '$weight', '$workoutType')";
} elseif ($goal == "Lose Weight") {
    $sql = "INSERT INTO workouts (exercise, duration, calories, workout_type) 
            VALUES ('Yoga', '$duration', '$calories', '$workoutType')";
} elseif ($goal == "Increase Muscle Mass") {
    $sql = "INSERT INTO workouts (exercise, reps, sets, weight, workout_type) 
            VALUES ('Strength', '$reps', '$sets', '$muscleMass', '$workout')";
} elseif ($goal == "Increase Stamina") {
    $sql = "INSERT INTO workouts (exercise, duration, distance, workout_type) 
            VALUES ('Cardio', '$duration', '$distance', '$workoutType')";
} else {
    echo json_encode(["message" => "Invalid goal"]);
    exit();
}

// Insert data into MySQL
if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Goal update logged successfully!"]);
} else {
    echo json_encode(["message" => "Error: " . $conn->error]);
}

$conn->close();
?>
