<?php
header("Content-Type: application/json");  // Send JSON response

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';  // Use the correct database name
$username = 'ejerrier';  // Use the correct MySQL username
$password = '1788128';  // Use the correct MySQL password

// Retrieve form data
$goal = $_POST['goal'];
$user_id = intval($_GET['user_id']);

// Prepare the SQL query based on workout type
if ($goal == 0) {
    $sql = "UPDATE users SET goals = '$goal' WHERE user_id = $user_id";  // Update user goal
} elseif ($goal == 1) {
    $sql = "UPDATE users SET goals = '$goal' WHERE user_id = $user_id";  // Update user goal
} elseif ($goal == 2) {
    $sql = "UPDATE users SET goals = '$goal' WHERE user_id = $user_id";  // Update user goal
} elseif ($goal == 3) {
    $sql = "UPDATE users SET goals = '$goal' WHERE user_id = $user_id";  // Update user goal
} elseif ($goal == 4) {
    $sql = "UPDATE users SET goals = '$goal' WHERE user_id = $user_id";  // Update user goal
} else {
    echo json_encode(["message" => "Invalid goal"]);
    exit();
}

// Insert data into MySQL
if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Goal update logged successfully!"]);
} else {
    echo json_encode(["message" => "Error: " . $conn->error]);
}

$conn->close();
?>
