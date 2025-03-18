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
    exit(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

$category = isset($_POST['category']) ? $_POST['category'] : 'calories';

$column = '';
switch ($category) {
    case 'calories':
        $column = 'caloriesBurned';
        break;
    case 'steps':
        $column = 'stepsTaken';
        break;
    case 'distance':
        $column = 'distanceMiles';
        break;
    default:
        exit(json_encode(['error' => 'Invalid category']));
}

$sql = "SELECT CONCAT(users.firstName, ' ', users.lastName) AS fullName, 
               workouts.$column AS currentStatus, 
               workouts.duration 
        FROM users
        LEFT JOIN workouts ON users.user_id = workouts.userID
        ORDER BY currentStatus DESC";

$stmt = $db->prepare($sql);
$stmt->execute();

$rank = 1;
$results = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $goal = 2000; // Default goal for calculation
    
    if ($category === 'steps') {
        $goal = 10000; // Example step goal
    } elseif ($category === 'distance') {
        $goal = 5; // Example distance goal in miles
    }
    
    $percentage = !is_null($row['currentStatus']) ? round(($row['currentStatus'] / $goal) * 100, 2) . '%' : 'No data';
    
    $results[] = [
        'rank' => $rank++,
        'fullName' => $row['fullName'],
        'goal' => $row['duration'] ? $row['duration'] . ' mins' : 'mins',
        'currentStatus' => $row['currentStatus'] ? $row['currentStatus'] : 'kcal',
        'percentage' => $percentage
    ];
}

echo json_encode($results);

