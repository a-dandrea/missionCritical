<?php
header("Content-Type: application/json");  // Send JSON response
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

// Database connection parameters
$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// Get form inputs safely (with default values)
$user_id = $_SESSION['user_id'];
$date = $_POST['date'] ?? null;
$sleep = $_POST['sleep'] ?? 0;
$steps = $_POST['steps'] ?? 0;
$water = $_POST['water'] ?? 0;
$active_minutes = $_POST['active_minutes'] ?? 0;
$outdoor_time = $_POST['outdoor_time'] ?? 0;

try {
    $db->beginTransaction();  // Start transaction

    // Insert into sleep table
    $sql1 = "INSERT INTO daily_sleep_log (user_id, date, daily_sleep_hours) 
             VALUES (:user_id, :date, :sleep) 
             ON DUPLICATE KEY UPDATE daily_sleep_hours = :sleep";
    $stmt1 = $db->prepare($sql1);
    $stmt1->execute(['user_id' => $user_id, 'date' => $date, 'sleep' => $sleep]);

    // Insert into steps table
    $sql2 = "INSERT INTO daily_steps (user_id, date, daily_step_count) 
             VALUES (:user_id, :date, :steps) 
             ON DUPLICATE KEY UPDATE daily_step_count = :steps";
    $stmt2 = $db->prepare($sql2);
    $stmt2->execute(['user_id' => $user_id, 'date' => $date, 'steps' => $steps]);

    // Insert into water intake table
    $sql3 = "INSERT INTO daily_water_intake (user_id, date, daily_water_intake) 
             VALUES (:user_id, :date, :water) 
             ON DUPLICATE KEY UPDATE daily_water_intake = :water";
    $stmt3 = $db->prepare($sql3);
    $stmt3->execute(['user_id' => $user_id, 'date' => $date, 'water' => $water]);

    // Insert into active minutes table
    $sql4 = "INSERT INTO daily_active_minutes (user_id, date, daily_active_minutes) 
             VALUES (:user_id, :date, :active_minutes) 
             ON DUPLICATE KEY UPDATE daily_active_minutes = :active_minutes";
    $stmt4 = $db->prepare($sql4);
    $stmt4->execute(['user_id' => $user_id, 'date' => $date, 'active_minutes' => $active_minutes]);

    // Insert into time outdoors table
    $sql5 = "INSERT INTO daily_time_outdoors (user_id, date, daily_time_outdoors) 
             VALUES (:user_id, :date, :outdoor_time) 
             ON DUPLICATE KEY UPDATE daily_time_outdoors = :outdoor_time";
    $stmt5 = $db->prepare($sql5);
    $stmt5->execute(['user_id' => $user_id, 'date' => $date, 'outdoor_time' => $outdoor_time]);

    $db->commit(); // Commit transaction

    // Return success response
    echo json_encode(["status" => "success", "message" => "Journal entry saved successfully"]);
} catch (PDOException $e) {
    $db->rollBack(); // Rollback transaction if an error occurs
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
}
?>
