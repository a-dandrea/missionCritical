<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Adjust the database connection parameters to match your setup.
$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';  // Use the correct database name
$username = 'ejerrier';  // Use the correct MySQL username
$password = '1788128';  // Use the correct MySQL password

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit();
}

//Fetch workouts from the database to display on the page
$query = "SELECT * FROM workouts WHERE userID = :userID ORDER BY workoutID DESC"; // Use named placeholder :user_id
$stmt = $db->prepare($query);
$stmt->bindParam(':userID', $_SESSION['userID'], PDO::PARAM_INT); // Bind the user_id parameter
$stmt->execute();

// Fetch all results
$workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <input type="hidden" name="userID" value="<?php echo $_SESSION['user_id']; ?>">
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


        <h3>Your Previous Workouts</h3>
        <!-- Display workouts from the database -->
        <table>
            <thead>
                <tr>
                    <th>Workout ID</th>
                    <th>Workout Type</th>
		    <th>Duration</th>
                    <th>Calories Burned</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($workouts): ?>
                    <?php foreach ($workouts as $workout): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($workout['workoutID']); ?></td>
                            <td><?php echo htmlspecialchars($workout['workoutType']); ?></td>
                            <td><?php echo htmlspecialchars($workout['duration']); ?></td>
			    <td><?php echo htmlspecialchars($workout['caloriesBurned']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No workouts logged yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="assets/script.js"></script>  <!-- Link to script.js -->
</body>
</html>
