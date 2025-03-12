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
            <!-- Workout Type Selection -->
            <label for="workout-type">Workout Type:</label>
            <select id="workout-type" name="workout-type" required>
                <option value="">Select a Workout Type</option>
                <option value="cardio">Cardio</option>
                <option value="strength">Strength</option>
                <option value="cycling">Cycling</option>
            </select>

            <!-- Dynamic Workout Fields will be injected here -->
            <div id="dynamic-fields"></div>

            <button type="submit">Submit Workout</button>
        </form>
    </div>

    <script src="assets/script.js"></script>  <!-- Link to script.js -->
</body>
</html>
