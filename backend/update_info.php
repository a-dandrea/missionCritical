<?php
header("Content-Type: application/json");  // Send JSON response
session_start();

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
    // Retrieve input data and validate it
    $age = isset($_POST['age']) ? intval($_POST['age']) : null;
    $height = isset($_POST['height']) ? floatval($_POST['height']) : null;
    $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : null;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

    // Check if all required fields are present
    if ($age === null || $height === null || $weight === null) {
        echo json_encode(["message" => "Invalid input data."]);
        exit();
    }

    // Update user data in a single query
    try {
        $stmt = $db->prepare("UPDATE users SET age = :age, weight = :weight, height = :height WHERE user_id = :user_id");
        $stmt->execute([
            ":age" => $age,
            ":weight" => $weight,
            ":height" => $height,
            ":user_id" => $user_id
        ]);

        echo json_encode(["message" => "User data updated successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["message" => "Invalid request method. Use POST."]);
}
?>
