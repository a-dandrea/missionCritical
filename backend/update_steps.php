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
    $age = isset($_POST['age']) && $_POST['age'] !== '' ? intval($_POST['age']) : null;
    $height = isset($_POST['height']) && $_POST['height'] !== '' ? floatval($_POST['height']) : null;
    $weight = isset($_POST['weight']) && $_POST['weight'] !== '' ? floatval($_POST['weight']) : null;

    error_log("Received - Age: " . ($age ?? 'Not provided') . ", Height: " . ($height ?? 'Not provided') . ", Weight: " . ($weight ?? 'Not provided'));

    // Check if at least one field is provided
    if ($age === null && $height === null && $weight === null) {
        echo json_encode(["message" => "No data provided to update."]);
        exit();
    }

    try {
        $stmt = $db->prepare("SELECT age, height, weight FROM users WHERE user_id = :user_id");
        $stmt->execute([":user_id" => $user_id]);
        $currentData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentData) {
            echo json_encode(["message" => "User not found."]);
            exit();
        }

        // Keep old values if new ones are not provided
        $age = $age ?? $currentData['age'];
        $height = $height ?? $currentData['height'];
        $weight = $weight ?? $currentData['weight'];

        // Perform the update
        $stmt = $db->prepare("UPDATE users SET age = :age, height = :height, weight = :weight WHERE user_id = :user_id");
        $stmt->execute([
            ":age" => $age,
            ":height" => $height,
            ":weight" => $weight,
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
