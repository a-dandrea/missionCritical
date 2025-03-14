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
    $goals = isset($_POST['goals']) ? intval($_POST['goals']) : null;

    error_log("Received - Goal: " . ($goals ?? 'Not provided'));

    if ($goals === null) {
        echo json_encode(["message" => "Invalid input data."]);
        exit();
    }

    try {
        $stmt = $db->prepare("SELECT goals FROM users WHERE user_id = :user_id");
        $stmt->execute([":user_id" => $user_id]);
        $currentData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentData) {
            echo json_encode(["message" => "User not found."]);
            exit();
        }

        $goals = $goals ?? $currentData['goals'];

        $stmt = $db->prepare("UPDATE users SET goals = :goals WHERE user_id = :user_id");
        $stmt->execute([
            ":goals" => $goals,
            ":user_id" => $user_id
        ]);
        
        error_log("Rows affected: " . $stmt->rowCount()); // Debugging
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(["message" => "Goal updated successfully!"]);
        } else {
            echo json_encode(["message" => "No changes made."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["message" => "Invalid request method. Use POST."]);
}
?>