<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("Database connection failed: " . $e->getMessage());
}
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
                    <th>Goal</th>
                    <th>Current Status</th>
                    <th>Percentage of Goal Completion</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "
                    SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullName, w.caloriesBurned, w.duration 
                    FROM users u
                    JOIN user_groups ug ON u.user_id = ug.user_id
                    JOIN groups g ON ug.group_id = g.group_id
                    JOIN workouts w ON u.user_id = w.userID
                ";
                
                $stmt = $db->prepare($sql);
                $stmt->execute();
                
                $rank = 1;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                        <td>{$rank}</td>
                        <td>{$row['fullName']}</td>
                        <td>{$row['duration']} mins</td>
                        <td>{$row['caloriesBurned']} kcal</td>
                        <td>" . round(($row['caloriesBurned'] / 2000) * 100, 2) . "%</td>
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

