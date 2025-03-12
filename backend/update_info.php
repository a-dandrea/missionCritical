<?php
header("Content-Type: application/json");  
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "Unauthorized: User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];
error_log("Updating user with ID: " . $user_id);

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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $age = isset($_POST['age']) ? intval($_POST['age']) : null;
    $height = isset($_POST['height']) ? floatval($_POST['height']) : null;
    $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : null;

    error_log("Received - Age: $age, Height: $height, Weight: $weight");

    if ($age === null || $height === null || $weight === null) {
        echo json_encode(["message" => "Invalid input data."]);
        exit();
    }

    try {
        $stmt = $db->prepare("UPDATE users SET age = :age, weight = :weight, height = :height WHERE user_id = :user_id");
        $stmt->execute([
            ":age" => $age,
            ":weight" => $weight,
            ":height" => $height,
            ":user_id" => $user_id
        ]);

        error_log("Rows affected: " . $stmt->rowCount());

        if ($stmt->rowCount() > 0) {
            echo json_encode(["message" => "User data updated successfully!"]);
        } else {
            echo json_encode(["message" => "No rows updated. Check user ID or values."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["message" => "Invalid request method. Use POST."]);
}
?>
