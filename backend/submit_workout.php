<?php
header("Content-Type: application/json");  // Send JSON response
session_start();

// Adjust database connection parameters
$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// Retrieve form data
$userID = $_POST['userID'] ?? null;
$workoutType = $_POST['workout-type'] ?? null;
$duration = $_POST['duration'] ?? null;
$calories = $_POST['calories'] ?? null;
//$startTime = date('Y-m-d H:i:s');  // Assume workout starts now
//$endTime = date('Y-m-d H:i:s', strtotime("+$duration minutes"));
$chosenStart = $_POST['workout-date'] ?? null;

if (!$chosenStart) {
    echo json_encode(["message" => "Workout date is required"]);
    exit();
}

// Convert to datetime format (strip 'T' and ensure correct format)
$startTime = date('Y-m-d H:i:s', strtotime($chosenStart));
$endTime = date('Y-m-d H:i:s', strtotime("$chosenStart +$duration minutes"));

$notes = $_POST['notes'] ?? null;

if (!$userID || !$workoutType || !$duration || !$calories) {
    echo json_encode(["message" => "Missing required fields"]);
    exit();
}

// Insert workout data
try {
    $sql = "INSERT INTO workouts (userID, workoutType, duration, caloriesBurned, startTime, endTime, notes) 
            VALUES (:userID, :workoutType, :duration, :caloriesBurned, :startTime, :endTime, :notes)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':workoutType', $workoutType);
    $stmt->bindParam(':duration', $duration);
    $stmt->bindParam(':caloriesBurned', $calories);
    $stmt->bindParam(':startTime', $startTime);
    $stmt->bindParam(':endTime', $endTime);
    $stmt->bindParam(':notes', $notes);

    if ($stmt->execute()) {
        $workoutID = $db->lastInsertId();  // Get the last inserted workout ID

        // Insert exercise data if available
        if ($workoutType == 'strength' && isset($_POST['exerciseName'])) {
            $exerciseName = $_POST['exerciseName'];
            $sets = $_POST['sets'];
            $repsPerSet = $_POST['repsPerSet'];
            $weight = $_POST['weight'];

            $exerciseSQL = "INSERT INTO exercises (workoutID, exerciseName, sets, repsPerSet, weight) 
                            VALUES (:workoutID, :exerciseName, :sets, :repsPerSet, :weight)";
            $exerciseStmt = $db->prepare($exerciseSQL);
            $exerciseStmt->bindParam(':workoutID', $workoutID);
            $exerciseStmt->bindParam(':exerciseName', $exerciseName);
            $exerciseStmt->bindParam(':sets', $sets);
            $exerciseStmt->bindParam(':repsPerSet', $repsPerSet);
            $exerciseStmt->bindParam(':weight', $weight);
            $exerciseStmt->execute();
        }

        echo json_encode(["message" => "Workout logged successfully!"]);
    } else {
        echo json_encode(["message" => "Error: Unable to log workout"]);
    }
} catch (PDOException $e) {
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}

$db = null;
?>

