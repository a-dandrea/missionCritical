<?php
header("Content-Type: application/json");  // Send JSON response

include('config.php');  // Database connection

// Retrieve form data
$goal = $_POST['goal'];
$user_id = intval($_GET['user_id']);

// Prepare the SQL query based on workout type
if ($goal == "No specific goal") {
    $sql = "UPDATE users SET goal = '$goal' WHERE user_id = $user_id";  // Update user goal
} elseif ($goal == "Maintain Weight") {
    $sql = "UPDATE users SET goal = '$goal' WHERE user_id = $user_id";  // Update user goal
} elseif ($goal == "Lose Weight") {
    $sql = "UPDATE users SET goal = '$goal' WHERE user_id = $user_id";  // Update user goal
} elseif ($goal == "Increase Muscle Mass") {
    $sql = "UPDATE users SET goal = '$goal' WHERE user_id = $user_id";  // Update user goal
} elseif ($goal == "Increase Stamina") {
    $sql = "UPDATE users SET goal = '$goal' WHERE user_id = $user_id";  // Update user goal
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
