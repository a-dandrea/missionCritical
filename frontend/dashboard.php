<?php
session_start();
require 'your_database_connection.php'; // Include your database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT name, email, age, gender, weight, heigh, goals, activity_level, privilege FROM users WHERE user_id = $user_id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Dashboard</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<header>
   <nav class="navbar">   
      <img src="images/rocket-icon.png" alt="Rocket Menu" class="rocket">
      <div class="nav-links">
         <a href="index.php">Home</a>
         <a href="dashboard.php">Dashboard</a>
         <a href="leaderboard.php">Leaderboard</a>
         <a href="workout.php">Workouts</a>
       </div>
   </nav>
</header>
<body>
<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($user['firstName']); ?>!</h1>

    <!-- Basic Info Box -->
    <div class="box">
        <h2>Basic Information</h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
        <p><strong>Weight:</strong> <?php echo htmlspecialchars($user['weight']); ?> lbs</p>
        <p><strong>Height:</strong> <?php echo htmlspecialchars($user['heigh']); ?> in</p>
    </div>

    <!-- Goal & Activity Box -->
    <div class="box">
        <h2>Current Goal</h2>
        <p><strong>Goal:</strong> <?php echo htmlspecialchars($user['goal']); ?></p>
        <p><strong>Activity Level:</strong> <?php echo htmlspecialchars($user['activity_level']); ?></p>
        <p><strong>Privilege:</strong> <?php echo htmlspecialchars($user['privilege']); ?></p>
    </div>

    <!-- Action Buttons -->
    <a href="update_goal.php" class="btn btn-update">Update Goal</a>
    <a href="submit_workout.php" class="btn">Add Workout</a>
</div>

</body>
</html>
