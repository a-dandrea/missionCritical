<?php
header("Content-Type: application/json");  
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
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// Ensure data is coming from a POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve input data and filter out empty values
    $daily_step_goal = isset($_POST['daily_step_goal']) && $_POST['daily_step_goal'] !== '' ? intval($_POST['daily_step_goal']) : null;

    error_log("Received - Daily Step Goal: " . ($daily_step_goal ?? 'Not provided'));

    // Check if at least one field is provided
    if ($daily_step_goal === null) {
        echo json_encode(["message" => "No step goal provided to update."]);
        exit();
    }

    try {
        $stmt = $db->prepare("SELECT daily_step_goal FROM progress WHERE user_id = :user_id");
        $stmt->execute([":user_id" => $user_id]);
        $currentData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentData) {
            echo json_encode(["message" => "User not found."]);
            exit();
        }

        // Keep old values if new ones are not provided
        $daily_step_goal = $daily_step_goal ?? $currentData['daily_step_goal'];

        // Perform the update
        $stmt = $db->prepare("UPDATE progress SET daily_step_goal = :daily_step_goal WHERE user_id = :user_id");
        $stmt->execute([
            ":daily_step_goal" => $daily_step_goal,
            ":user_id" => $user_id
        ]);

        error_log("Rows affected: " . $stmt->rowCount()); // Debugging

        if ($stmt->rowCount() > 0) {
            echo json_encode(["message" => "User info updated successfully!"]);
        } else {
            echo json_encode(["message" => "No changes made. Data is the same as before."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["message" => "Invalid request method. Use POST."]);
}
?>
