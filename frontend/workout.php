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
// Handle workout type filter
$selectedType = $_GET['filter'] ?? 'all';

// Build query dynamically
if ($selectedType !== 'all') {
    $query = "SELECT * FROM workouts WHERE userID = :userID AND workoutType = :workoutType ORDER BY workoutID DESC";
} else {
    $query = "SELECT * FROM workouts WHERE userID = :userID ORDER BY workoutID DESC";
}

try {
    $stmt = $db->prepare($query);
    $stmt->bindParam(':userID', $_SESSION['user_id'], PDO::PARAM_INT);
    if ($selectedType !== 'all') {
        $stmt->bindParam(':workoutType', $selectedType, PDO::PARAM_STR);
    }
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
    <title>Workout</title>
    <link rel="icon" href="images/astronaut.png">
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
         <a href="journal.php">Mission Logs</a>
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
        <h1 style="text-align: center;">Your Previous Workouts</h1>
        <form method="get" style="text-align: center; margin-bottom: 20px;">
            <label for="filter">Filter Workouts:</label>
            <select name="filter" id="filter" onchange="this.form.submit()">
               <option value="all" <?php if ($selectedType == 'all') echo 'selected'; ?>>Show All Workouts</option>
               <option value="Strength/ Weight Training" <?php if ($selectedType == 'Strength/ Weight Training') echo 'selected'; ?>>Strength/Weight Training</option>
               <option value="Running" <?php if ($selectedType == 'Running') echo 'selected'; ?>>Running</option>
               <option value="Cycling" <?php if ($selectedType == 'Cycling') echo 'selected'; ?>>Cycling</option>
               <option value="Other Workout" <?php if ($selectedType == 'Other Workout') echo 'selected'; ?>>Other Workout</option>
            </select>
         </form>

        <table>
            <thead>
                <tr>
                    <th>Workout Type</th>
                    <th>Duration</th>
                    <th>Calories Burned</th>
                    <th>Average Heart Rate</th>
                    <th>Workout Date</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($workouts): ?>
                    <?php foreach ($workouts as $workout): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($workout['workoutType']); ?></td>
                            <td><?php echo htmlspecialchars($workout['duration']); ?></td>
                            <td><?php echo htmlspecialchars($workout['caloriesBurned']); ?></td>
                            <td><?php echo htmlspecialchars($workout['heartRate'] ?? ''); ?></td>
                            <td><?php echo date("F j, Y", strtotime($workout['startTime'])); ?></td>
                            <td><?php echo htmlspecialchars($workout['notes'] ?? ''); ?></td>
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

        <div class="container">
        <h1>Log Your Workout</h1>
        <form id="workout-form">
            <input type="hidden" name="userID" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
            <label for="workout-type" style="text-align: center;">Workout Type:</label>
            <select id="workout-type" name="workout-type" required>
                <option value="">Select a Workout Type</option>
                <option value="Strength/ Weight Training">Strength/Weight Training</option>
                <option value="Running">Running</option>
                <option value="Cycling">Cycling</option>
                <option value="Other Workout">Other Workout</option>
            </select>
            <label for="workout-date" style="text-align: center;">Workout Date:</label>
            <input type="date" id="workout-date" name="workout-date" required>

            <div id="dynamic-fields"></div>
            <button type="submit">Submit Workout</button>
        </form>
        </div>
        <div class="container">

        <!-- Creating a Workout Plan using AI -->
        <h2 style="text-align: center;">Need Workout Suggestions?</h2>
	<h4 style="text-align: center;">Fill out the options below to help AI create a plan for you!</h4>
        <form id="generate-form">
            <input type="hidden" name="userID" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>" />
            <div style="text-align: center; margin-bottom: 10px;">
               <label for="goal" style="display: block; width: 300px; margin: 0 auto; text-align: center;">
                   What is your goal?
               </label>
               <input type="text" name="goal" required style="width: 300px;" />
            </div>

            <div style="text-align: center; margin-bottom: 10px;">
                <label for="fitness_level" style="display: block; width: 300px; margin: 0 auto; text-align: center;">
                    What is your fitness level?
                </label>
                <input type="text" name="fitness_level" required style="width: 300px;" />
            </div>

            <div style="text-align: center; margin-bottom: 10px;">
                <label for="workout_type" style="display: block; width: 300px; margin: 0 auto; text-align: center;">
                    What types of workout would you prefer?
                </label>
                <input type="text" name="workout_type" required style="width: 300px;" />
            </div>

            <div style="text-align: center; margin-bottom: 10px;">
                <label for="time_per_session" style="display: block; width: 300px; margin: 0 auto; text-align: center;">
                    How long do you want each workout session to be?
                </label>
                <input type="text" name="time_per_session" required style="width: 300px;" />
            </div>

            <div style="text-align: center; margin-bottom: 10px;">
                <label for="days_per_week" style="display: block; width: 300px; margin: 0 auto; text-align: center;">
                    How many days per week do you want to workout?
                </label>
                <input type="number" min=0 max=7 name="days_per_week" required style="width: 300px;" />
            </div>

            <button type="submit">Generate Workout Plan</button>
        </form>

        <!-- Display result -->
        <h2 style="text-align:center;">Your AI-Generated Workout Plan</h2>
        <pre id="generated-plan" style="white-space: pre-wrap; background: #f3f3f3; padding: 10px;"></pre>


        <script>
        document.addEventListener("DOMContentLoaded", function () {
            console.log("Generate form script loaded.");

            const form = document.getElementById("generate-form");
            const output = document.getElementById("generated-plan");

            if (!form) {
                console.error("Form not found!");
                return;
            }

            form.addEventListener("submit", async function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const payload = Object.fromEntries(formData.entries());

                console.log("Sending payload to backend:", payload);
                try {  
                    const response = await fetch("../backend/generate_workout.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();
                    console.log("Response from backend:", result);
                    output.textContent = result.plan || result.message;
                } catch (err) {
                    console.error("Fetch failed:", err);
                    output.textContent = "Something went wrong. Check your network or API.";
                }
                
            });
        });
        </script>
        </div>
    </div>

    <script src="assets/script.js"></script>
</body>

<footer style="background: #0f0a66; color:white; padding: 10px 20px;">
    <p>&copy; Copyright Mission Critical Group</p>
</footer>
</html>