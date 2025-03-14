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

// Get the current user's ID (assuming it's stored in the session)
$current_user_id = $_SESSION['user_id'] ?? null;

if ($current_user_id) {
    // Get the group ID for the current user
    $groupQuery = "
        SELECT group_id 
        FROM user_groups 
        WHERE user_id = :user_id
    ";
    $stmt = $db->prepare($groupQuery);
    $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
    $stmt->execute();
    $group_id = $stmt->fetchColumn();

    if ($group_id) {
        // Get all users in the same group
        $leaderboardQuery = "
            SELECT u.username, u.goal, u.current_status,
                   (u.current_status / u.goal) * 100 AS completion_percentage
            FROM users u
            JOIN user_groups ug ON u.user_id = ug.user_id
            WHERE ug.group_id = :group_id
            ORDER BY completion_percentage DESC
        ";
        $stmt = $db->prepare($leaderboardQuery);
        $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
        $stmt->execute();
        $group_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $group_members = [];
    }
} else {
    $group_members = [];
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
                <?php if (!empty($group_members)): ?>
                    <?php $rank = 1; ?>
                    <?php foreach ($group_members as $member): ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($member['username']); ?></td>
                            <td><?php echo htmlspecialchars($member['goal']); ?></td>
                            <td><?php echo htmlspecialchars($member['current_status']); ?></td>
                            <td><?php echo round($member['completion_percentage'], 2) . '%'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No members found in your group.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="assets/leaderboard.js" defer></script>
</body>
</html>

