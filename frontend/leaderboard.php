<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("Database connection failed: " . $e->getMessage());
}

// Fetch leaderboard data
$sql = "
    SELECT 
        CONCAT(u.firstName, ' ', u.lastName) AS fullName,
        u.goals, 
        SUM(w.caloriesBurned) AS totalCaloriesBurned,
        SUM(w.duration) AS totalDuration,
        IF(u.goals > 0, ROUND((SUM(w.caloriesBurned) / u.goals) * 100, 2), 0) AS goalCompletion
    FROM users u
    JOIN workouts w ON u.user_id = w.userID
    GROUP BY u.user_id
    ORDER BY goalCompletion DESC;
";

$stmt = $db->prepare($sql);
$stmt->execute();
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

        <form method="POST" action="leaderboard.php" id="category-form">
            <label for="category">Choose Category:</label>
            <select name="category" id="category" onchange="updateLeaderboard()">
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
                    <th>Goal (kcal)</th>
                    <th>Calories Burned</th>
                    <th>Goal Completion (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rank = 1;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                        <td>{$rank}</td>
                        <td>{$row['fullName']}</td>
                        <td>{$row['goals']} kcal</td>
                        <td>{$row['totalCaloriesBurned']} kcal</td>
                        <td>{$row['goalCompletion']}%</td>
                    </tr>";
                    $rank++;
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="assets/leaderboard.js" defer></script>
</body>
</html>
/div>

    <script src="assets/leaderboard.js" defer></script>
</body>
</html>

