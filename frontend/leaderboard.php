<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <img src="images/rocket-icon.png" alt="Rocket Menu" class="rocket">
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="leaderboard.php">Leaderboard</a>
                <a href="workout.php">Workouts</a>
                <?php if ($isLoggedIn): ?>
                    <a href="logout.php" class="logout-button">Logout</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <div class="container">
        <h2>Leaderboard</h2>

        <form id="category-form">
            <label for="category">Choose Category:</label>
            <select name="category" id="category">
                <option value="calories">Calories</option>
                <option value="steps">Steps</option>
                <option value="distance">Distance (miles)</option>
            </select>
        </form>

        <table id="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>User</th>
                    <th>Goal</th>
                    <th>Current Status</th>
                    <th>Goal Completion (%)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be dynamically inserted here -->
            </tbody>
        </table>
    </div>

    <script src="assets/leaderboard.js" defer></script>
</body>
</html>

