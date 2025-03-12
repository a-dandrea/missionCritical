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

$age = $_POST['age'];
$height = $_POST['height'];
$weight = $_POST['weight'];
$user_id = intval($_POST['user_id']);

// Update user's goal in the database
try {
    $stmt = $db->prepare("UPDATE users SET age = :age WHERE user_id = :user_id");
    $stmt->execute([
        ":age" => $age,
        ":user_id" => $user_id
    ]);
    $stmt = $db->prepare("UPDATE users SET weight = :weight WHERE user_id = :user_id");
    $stmt->execute([
        ":weight" => $weight,
        ":user_id" => $user_id
    ]);
    $stmt = $db->prepare("UPDATE users SET height = :height WHERE user_id = :user_id");
    $stmt->execute([
        ":height" => $height,
        ":user_id" => $user_id
    ]);

    echo json_encode(["message" => "Goal updated successfully!"]);
} catch (PDOException $e) {
    echo json_encode(["message" => "Error: " . $e->getMessage()]);
}
?>
