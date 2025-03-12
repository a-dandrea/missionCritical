<?php
header("Content-Type: application/json");  // Send JSON response

$host = "joecool.highpoint.edu";
$username = "knguyen";
$password = "knguyen1871644";
$database = "csc4710_S25_missioncritical";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}

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
