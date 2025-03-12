<?php
include('../backend/config.php');  // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Your Workout</title>
    <link rel="stylesheet" href="style.css">  <!-- Link to styles.css -->
    
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
        <h2>Log Your Workout</h2>

        <form id="workout-form">
            <label for="exercise">Exercise Type:</label>
            <input type="text" id="exercise" name="exercise" required>

            <label for="duration">Duration (minutes):</label>
            <input type="number" id="duration" name="duration" required>

            <label for="reps">Reps (if applicable):</label>
            <input type="number" id="reps" name="reps">

            <label for="distance">Distance (km, if applicable):</label>
            <input type="number" id="distance" name="distance">

            <label for="calories">Calories Burned:</label>
            <input type="number" id="calories" name="calories" required>

            <button type="submit">Submit Workout</button>
        </form>
    </div>

    <script src="assets/script.js"></script>  <!-- Link to script.js -->
</body>
</html>
