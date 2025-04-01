<?php
header("Content-Type: application/json");  
session_start();

error_log("Raw POST data: " . print_r($_POST, true));  // Debugging

if (empty($_POST)) {
    echo json_encode(["message" => "No POST data received. Check if the form is submitting correctly."]);
    exit();
}

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
    $daily_outside_goal = isset($_POST['daily_outside_goal']) && $_POST['daily_outside_goal'] !== '' ? floatval($_POST['daily_outside_goal']) : null;

    error_log("Received - Daily Outside Time Goal: " . ($daily_outside_goal ?? 'Not provided'));

    // Check if at least one field is provided
    if ($daily_outside_goal === null) {
        echo json_encode(["message" => "No Outside Time goal provided to update."]);
        exit();
    }

    try {
        $stmt = $db->prepare("SELECT daily_outside_goal FROM users WHERE user_id = :user_id");
        $stmt->execute([":user_id" => $user_id]);
        $currentData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentData) {
            echo json_encode(["message" => "User not found."]);
            exit();
        }

        // Keep old values if new ones are not provided
        $daily_outside_goal = $daily_outside_goal ?? $currentData['daily_outside_goal'];

        // Perform the update
        $stmt = $db->prepare("UPDATE users SET daily_outside_goal = :daily_outside_goal WHERE user_id = :user_id");
        $stmt->execute([
            ":daily_outside_goal" => $daily_outside_goal,
            ":user_id" => $user_id
        ]);

        error_log("Rows affected: " . $stmt->rowCount()); // Debugging

        if ($stmt->rowCount() > 0) {
            echo json_encode(["message" => "Outside Time goal updated successfully!"]);
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
