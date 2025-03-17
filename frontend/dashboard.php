<?php
session_start();

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT firstName, lastName, email, age, gender, weight, height, goals, activity_level, privilege FROM users WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }

    // Fetch user's groups
    $sql = "SELECT g.username FROM groups g JOIN user_groups ug ON g.group_id = ug.group_id WHERE ug.user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

$stmt->closeCursor();
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

    <div class="box">
        <h2>Basic Information</h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
        <p><strong>Weight:</strong> <?php echo htmlspecialchars($user['weight']); ?> lbs</p>
        <p><strong>Height:</strong> <?php echo htmlspecialchars($user['height']); ?> in</p>
    </div>

    <div class="box">
        <h2>Your Groups</h2>
        <?php if (!empty($groups)): ?>
            <p><strong>Groups:</strong> <?php echo implode(', ', array_column($groups, 'username')); ?></p>
        <?php else: ?>
            <p>You are not currently in any groups.</p>
        <?php endif; ?>
    </div>

    <a href="personalinfo.php"><button type="button">Update Basic Information</button></a>
    <a href="goals.php"><button type="button">Update Goal</button></a>
    <a href="workout.php"><button type="button">Add Workout</button></a>
    <a href="group_membership.php"><button type="button">Create Group</button></a>
</div>
</body>
</html>

