<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit(json_encode(["error" => "Database connection failed: " . $e->getMessage()]));
}

$category = $_POST['category'] ?? 'calories'; // Default to calories if no selection

// Determine which column to pull based on category
$column = 'caloriesBurned'; // Default
$goal = 'duration'; // Default goal (time spent)

if ($category === 'steps') {
    $column = 'stepsTaken';
    $goal = 'stepGoal';
} elseif ($category === 'distance') {
    $column = 'distanceMiles';
    $goal = 'distanceGoal';
}

$sql = "SELECT CONCAT(users.firstName, ' ', users.lastName) AS fullName, 
               workouts.$column AS currentValue, 
               workouts.$goal AS goalValue
        FROM users
        LEFT JOIN workouts ON users.user_id = workouts.userID";

$stmt = $db->prepare($sql);
$stmt->execute();

$rank = 1;
$rows = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $percentage = (!is_null($row['currentValue']) && !is_null($row['goalValue']) && $row['goalValue'] > 0) 
        ? round(($row['currentValue'] / $row['goalValue']) * 100, 2) . "%" 
        : "No data";

    $rows[] = [
        "rank" => $rank,
        "fullName" => $row['fullName'],
        "goal" => !is_null($row['goalValue']) ? $row['goalValue'] . ($category === 'distance' ? ' miles' : ' mins') : 'No data',
        "currentValue" => !is_null($row['currentValue']) ? $row['currentValue'] . ($category === 'distance' ? ' miles' : ' kcal') : 'No data',
        "percentage" => $percentage
    ];
    $rank++;
}

echo json_encode($rows);

