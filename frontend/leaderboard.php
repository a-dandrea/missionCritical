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
            </div>
        </nav>
    </header>

    <div class="container">
        <h2>Leaderboard</h2>

        <!-- Category Dropdown -->
        <form method="POST" action="leaderboard.php" id="category-form">
            <label for="category">Choose Category:</label>
            <select name="category" id="category" onchange="updateLeaderboard()">
                <option value="calories">Calories</option>
                <option value="steps">Steps</option>
                <option value="distance">Distance (miles)</option>
            </select>
        </form>

        <!-- Leaderboard Table -->
        <table id="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>User</th>
                    <th>Goal</th>
                    <th>Current Status</th>
                    <th>Percentage of Goal Completion</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be injected here using JavaScript -->
            </tbody>
        </table>
    </div>

    <script src="assets/leaderboard.js" defer></script>
</body>
</html>
