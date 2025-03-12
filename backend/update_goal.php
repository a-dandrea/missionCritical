<?php
header("Content-Type: application/json");  
session_start();

// Check if the user is logged in and has a user_id in the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "Unauthorized: User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id']; // Get user_id from session

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
    $goal = isset($_POST['goal']) ? intval($_POST['goal']) : null;

    if ($goal === null) {
        echo json_encode(["message" => "Invalid input data."]);
        exit();
    }

    try {
        $stmt = $db->prepare("SELECT goal FROM users WHERE user_id = :user_id");
        $stmt->execute([":user_id" => $user_id]);
        $currentData = $stmt->fetch(PDP::FETCH_ASSOC);

        if (!$currentData) {
            echo json_encode(["message" => "User not found."]);
            exit();
        }

        $stmt = $db->prepare("UPDATE users SET goals = :goal WHERE user_id = :user_id");
        $stmt->execute([
            ":goal" => $goal,
            ":user_id" => $user_ic
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
