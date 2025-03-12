<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database connection
$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Your Goals</title>
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
        <h2>Update Your Fitness Goal</h2>

        <form id="update-goal-form">
            <label for="goal">Select Goal:</label>
            <select id="goal" name="goal" required>
                <option value="">Select a Goal</option>
                <option value=0>Maintain Weight</option>
                <option value=1>Lose Weight</option>
                <option value=2>Increase Muscle Mass</option>
                <option value=3>Increase Stamina</option>
            </select>

            <button type="update">Update Goal</button>
        </form>
    </div>

    <script src="assets/goal.js"></script>  <!-- Link to script.js -->

</body>
</html>
