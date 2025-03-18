<?php
header("Content-Type: application/json");  
session_start();

// Check if the user is logged in and has a user_id in the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "Unauthorized: User not logged in."]);
    exit();
}

$userID = $_SESSION['user_id']; // Get user_id from session
error_log("Updating user with ID: " . $userID); // Debugging

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
    $weight = isset($_POST['weight']) && $_POST['weight'] !== '' ? floatval($_POST['weight']) : null;

    error_log("Received - Weight: " . ($weight ?? 'Not provided'));

    // Check if at least one field is provided
    if ($weight === null) {
        echo json_encode(["message" => "No data provided to update."]);
        exit();
    }

    try {
        $stmt = $db->prepare("SELECT weight FROM progress WHERE userID = :userID");
        $stmt->execute([":userID" => $userID]);
        $currentData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentData) {
            echo json_encode(["message" => "User not found."]);
            exit();
        }

        // Keep old values if new ones are not provided
        $weight = $weight ?? $currentData['weight'];

        // Perform the update
        $stmt = $db->prepare("UPDATE progress SET weight = :weight WHERE userID = :userID");
        $stmt->execute([
            ":weight" => $weight,
            ":userID" => $userID
        ]);

        error_log("Rows affected: " . $stmt->rowCount()); // Debugging

        if ($stmt->rowCount() > 0) {
            echo json_encode(["message" => "Progress updated successfully!"]);
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
