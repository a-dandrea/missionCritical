<?php
include('../backend/config.php');  // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Your Goals</title>
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
        <h2>Update Your Goals</h2>

        <form id="goal-update-form">

            <!-- Goal Selection -->
            <label for="goal">Goal to Update:</label>
            <select id="goal" name="goal" required>
                <option value=""> Select New Goal</option>
                <option value="No specific goal">No specific goal</option>
                <option value="Maintain weight">Maintain weight</option>
                <option value="Lose weight">Lose weight</option>
                <option value="Increase Muscle Mass">Increas Muscle Mass</option>
                <option value="Increase Stamina">Increase Stamina</option>
            </select>
            <div id="dynamic-fields"></div>

            <button type="update">Update Goal</button>
        </form>
    </div>

    <script src="assets/goal.js"></script>  <!-- Link to goal.js -->
</body>
</html>
