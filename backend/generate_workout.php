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
$userID = $data['userID'] ?? null;
$goal = $data['goal'] ?? null;
$level = $data['level'] ?? null;
$type = $data['workout_type'] ?? null;
$time = $data['time_per_session'] ?? null;
$days = $data['days_per_week'] ?? null;

// Validate input
if (!$userID || !$goal || !$level || !$type || !$time || !$days) {
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
  "model" => "gpt-4",
  "messages" => [["role" => "user", "content" => $prompt]]
]);

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$plan = $result['choices'][0]['message']['content'] ?? null;

if (!$plan) {
    echo json_encode(["message" => "OpenAI returned no plan", "raw" => $result]);
    exit();
}

// Save to DB
$sql = "INSERT INTO workout_plans (user_id, goal, level, workoutType, time_per_session, days_per_week, plan) 
        VALUES (:userID, :goal, :level, :type, :time, :days, :plan)";

$stmt = $db->prepare($sql);
$stmt->bindParam(':userID', $user_id);
$stmt->bindParam(':goal', $goal);
$stmt->bindParam(':level', $level);
$stmt->bindParam(':type', $type);
$stmt->bindParam(':time', $time);
$stmt->bindParam(':days', $days);
$stmt->bindParam(':plan', $plan);
$stmt->execute();

echo json_encode(["message" => "Workout plan generated successfully!", "plan" => $plan]);

?>

