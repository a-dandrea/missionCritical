<?php
date_default_timezone_set('America/New_York');
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in

// Debugging session data
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (!isset($_SESSION['user_id'])) {
    echo "Error: User not logged in.";
    exit();
}

// Adjust the database connection parameters to match your setup.
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

// Fetch workouts from the database to display on the page
$query = "SELECT * FROM workouts WHERE userID = :userID ORDER BY workoutID DESC";
try {
    $stmt = $db->prepare($query);
    $stmt->bindParam(':userID', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching workouts: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Your Workout</title>
    <link rel="stylesheet" href="style.css">
</head>
<header>
<nav class="navbar">   
    <div class="dropdown">
      <a href="index.php" class="dropbtn">
         <img src="images/rocket-icon.png" alt="Rocket Menu" class="rocket">
      </a>
      <div class="dropdown-content">
         <a href="subscriptions.php">Subscriptions</a>
         <a href="payment.php">Payment</a>
      </div>
    </div>
    <div class="nav-links">
         <a href="dashboard.php">Dashboard</a>
         <a href="journal.php">Journal</a>
         <a href="leaderboard.php">Leaderboard</a>
         <a href="workout.php">Workouts</a>
         <a href="recipe.php">Recipes</a>
         <?php if ($isLoggedIn): ?>
            <a href="logout.php" class="logout-button">Logout</a>
         <?php endif; ?>
    </div>
    </nav>
</header>

<body>
    <div class="container">
        <h2>Log Your Workout</h2>

        <form id="workout-form">
            <input type="hidden" name="userID" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
            <label for="workout-type">Workout Type:</label>
            <select id="workout-type" name="workout-type" required>
                <option value="">Select a Workout Type</option>
                <option value="Other Workout">Other Workout</option>
                <option value="Strength/ Weight Training">Strength/ Weight Training</option>
                <option value="Running">Running</option>
                <option value="Cycling">Cycling</option>
            </select>
            <label for="workout-date">Workout Date:</label>
            <input type="date" id="workout-date" name="workout-date" required>

            <label for="step-count">Step Count:</label>
            <input type="number" id="step-count" name="step-count" min="0">

            <div id="dynamic-fields"></div>
            <button type="submit">Submit Workout</button>
        </form>

        <h3>Your Previous Workouts</h3>
        <table>
            <thead>
                <tr>
                    <th>Workout Type</th>
                    <th>Duration</th>
                    <th>Calories Burned</th>
                    <th>Workout Date</th>
                    <th>Step Count</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($workouts): ?>
                    <?php foreach ($workouts as $workout): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($workout['workoutType']); ?></td>
                            <td><?php echo htmlspecialchars($workout['duration']); ?></td>
                            <td><?php echo htmlspecialchars($workout['caloriesBurned']); ?></td>
                            <td><?php echo htmlspecialchars($workout['startTime']); ?></td>
                            <td><?php echo htmlspecialchars($workout['stepCount'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No workouts logged yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="assets/script.js"></script>
</body>
</html>
