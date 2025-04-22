<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Construct OpenAI prompt
$prompt = "Create a weekly workout plan for:\n";
$prompt .= "Goal: $goal\nLevel: $level\nWorkout type: $type\n";
$prompt .= "Time per session: $time\nDays per week: $days\n";

// Call OpenAI API
$openai_key = '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';
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
curl_close($ch);

$result = json_decode($response, true);

file_put_contents("openai_raw_response.json", json_encode($result, JSON_PRETTY_PRINT));

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
$stmt->bindParam(':aiplan', $plan);
$stmt->execute();

echo json_encode(["message" => "Workout plan generated successfully!", "plan" => $plan]);

?>

