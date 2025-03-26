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

$user_id = $_SESSION['user_id']; // Get user_id from session
error_log("Updating user with ID: " . $user_id); // Debugging

// Database connection
$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

try {
   $db = new PDO($dsn, $username, $password);
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   error_log("Database connection failed: " . $e->getMessage());
   echo json_encode(["message" => "Database connection failed."]);
   exit();
}


// Ensure data is coming from a POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
   $goals = isset($_POST['goals']) ? $_POST['goals'] : [];

    if (!is_array($goals) || count($goals) < 1 || count($goals) > 4) {
        echo json_encode(["message" => "Invalid input data. Select between 1 and 4 goals."]);
        exit();
    }

    // Fill the goal columns, setting empty values if less than 4 goals are provided
    $goalValues = array_pad($goals, 4, NULL);

    try {
        $stmt = $db->prepare("UPDATE users SET goal1 = :goal1, goal2 = :goal2, goal3 = :goal3, goal4 = :goal4 WHERE user_id = :user_id");
        $stmt->execute([
            ":goal1" => $goalValues[0],
            ":goal2" => $goalValues[1],
            ":goal3" => $goalValues[2],
            ":goal4" => $goalValues[3],
            ":user_id" => $user_id
        ]);
        
        error_log("Rows affected: " . $stmt->rowCount());
        echo json_encode(["message" => "Goals updated successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["message" => "Invalid request method. Use POST."]);
}
?>