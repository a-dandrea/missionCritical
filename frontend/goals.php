<?php
session_start();

// Database connection
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

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "User not logged in.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Your Goals</title>
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
        <h2>Update Your Fitness Goal</h2>

        <form id="goal-update-form">
            <label for="goal">Select Goal:</label>
            <select id="goal" name="goal" required>
                <option value="">Select a Goal</option>
                <option value=0>Maintain Weight</option>
                <option value=1>Lose Weight</option>
                <option value=2>Increase Muscle Mass</option>
                <option value=3>Increase Stamina</option>
            </select>

            <button type="submit">Update Goal</button>
        </form>

        <p id="message"></p>  
    </div>

    <script>
        document.getElementById("goal-update-form").addEventListener("submit", function(event) {
            event.preventDefault();  // Prevent page reload

            const goal = document.getElementById("goal").value;
            const userId = <?php echo json_encode($user_id); ?>;

            fetch("update_goal.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `goal=${goal}&user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("message").textContent = data.message;
            })
            .catch(error => console.error("Error:", error));
        });
    </script>
</body>
</html>
