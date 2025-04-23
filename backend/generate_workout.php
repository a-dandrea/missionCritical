<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log'); 

session_start();

// Check if the user is logged in and has a user_id in the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "Unauthorized: User not logged in."]);
    exit();
}

// Get user_id from session
$user_id = $_SESSION['user_id']; 
// Debugging
error_log("Updating user with ID: " . $user_id); 

// DB config
$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

// DB connection
try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// Load POST input data
$data = json_decode(file_get_contents("php://input"), true);
$goal = $data['goal'] ?? null;
$level = $data['fitness_level'] ?? null;
$type = $data['workout_type'] ?? null;
$time = $data['time_per_session'] ?? null;
$days = $data['days_per_week'] ?? null;

// Validate input
if (!$goal || !$level || !$type || !$time || !$days) {
    echo json_encode(["message" => "Missing required fields."]);
    exit();
}

// Insert into database
try {
    $sql = "INSERT INTO ai_workout_plans(userID, goal, fitnessLevel, workoutType, timePerSession, daysPerWeek)
              VALUES (:userID, :goal, :level, :type, :time, :days)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':userID', $user_id);
    $stmt->bindParam(':goal', $goal);
    $stmt->bindParam(':level', $level);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':time', $time);
    $stmt->bindParam(':days', $days);

    $stmt->execute();
    //echo json_encode(["message" => "Workout plan saved successfully."]);
} catch (PDOException $e) {
    echo json_encode(["message" => "Failed to save workout: " . $e->getMessage()]);
}

// Getting latest workout input data from DB 
try {
    $sql = "SELECT goal, fitnessLevel, workoutType, timePerSession, daysPerWeek 
              FROM ai_workout_plans 
              WHERE userID = :userID 
              ORDER BY aiWorkoutID DESC 
              LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':userID', $user_id);
    $stmt->execute();
    $workout = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$workout) {
        echo json_encode(["message" => "No previous workout data found for user."]);
        exit();
    }

    $goal = $workout['goal'];
    $level = $workout['fitnessLevel'];
    $type = $workout['workoutType'];
    $time = $workout['timePerSession'];
    $days = $workout['daysPerWeek'];

} catch (PDOException $e) {
    echo json_encode(["message" => "Failed to retrieve workout data: " . $e->getMessage()]);
    exit();
}

// Construct OpenAI prompt
$prompt = "Create a weekly workout plan for:\n";
$prompt .= "Goal: $goal\nLevel: $level\nWorkout type: $type\n";
$prompt .= "Time per session: $time\nDays per week: $days\n";

// Send to OpenAI API
$openai_key = "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";
$headers = [
    "Content-Type: application/json",
    "Authorization: Bearer $openai_key"
];

$body = json_encode([
    "model" => "gpt-3.5-turbo",
    "messages" => [["role" => "user", "content" => $prompt]]
]);

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

$response = curl_exec($ch);

// checking if curl_exec failed
if ($response === false) {
    $error = curl_error($ch);
    error_log("cURL Error: $error");
    echo json_encode(["message" => "OpenAI API request failed", "error" => $error]);
    exit();
}

curl_close($ch);

$result = json_decode($response, true);

// Debug: Save raw OpenAI response
//file_put_contents("openai_raw_response.json", json_encode($result, JSON_PRETTY_PRINT));
error_log("OpenAI raw response:" . print_r($result, true));

$plan = $result['choices'][0]['message']['content'] ?? null;

if (!$plan) {
    echo json_encode([
        "message" => "OpenAI returned no plan",
        "raw_response" => $result,
        "prompt" => $prompt
    ]);
    exit();
}

// Save to DB
$sql = "INSERT INTO ai_workout_plans (userID, goal, fitnessLevel, workoutType, timePerSession, daysPerWeek, aiPlan) 
        VALUES (:userID, :goal, :fitnessLevel, :workoutType, :timePerSession, :daysPerWeek, :aiPlan)";

$stmt = $db->prepare($sql);
$stmt->bindParam(':userID', $user_id);
$stmt->bindParam(':goal', $goal);
$stmt->bindParam(':fitnessLevel', $level);
$stmt->bindParam(':workoutType', $type);
$stmt->bindParam(':timePerSession', $time);
$stmt->bindParam(':daysPerWeek', $days);
$stmt->bindParam(':aiPlan', $plan);
$stmt->execute();

//header("Content-Type: application/json");
//header("Content-Type: application/json");


echo json_encode([
    "message" => "Workout plan generated successfully!", 
    "plan" => $plan]);



