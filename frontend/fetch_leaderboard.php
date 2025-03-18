<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit(json_encode(["error" => "Database connection failed: " . $e->getMessage()]));
}

// Get category from AJAX request
$category = $_GET['category'] ?? 'calories';

// Map category to the correct column in the database
$columnMapping = [
    "calories" => "caloriesBurned",
    "steps" => "stepsTaken",
    "distance" => "distanceCovered"
];

if (!isset($columnMapping[$category])) {
    exit(json_encode(["error" => "Invalid category"]));
}

$column = $columnMapping[$category];

$sql = "
    SELECT 
        CONCAT(u.firstName, ' ', u.lastName) AS fullName,
        u.goals AS goal, 
        SUM(w.$column) AS currentStatus,
        IF(u.goals > 0, ROUND((SUM(w.$column) / u.goals) * 100, 2), 0) AS goalCompletion
    FROM users u
    JOIN workouts w ON u.user_id = w.userID
    GROUP BY u.user_id
    ORDER BY goalCompletion DESC;
";

$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);

