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
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Personal Info</title>
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
         <?php if ($isLoggedIn): ?>
            <a href="logout.php" class="logout-button">Logout</a>
         <?php endif; ?>
    </div>
</nav>
</header>

<body>
    <div class="container">
        <h2>Update Personal Information</h2>

        <form id="update-info-form">
        <input type="hidden" name="userID" value="<?php echo $_SESSION['user_id']; ?>">
            <!-- Workout Type Selection -->
            <label for="age">Age:</label>
            <input type="number" id="age" name="age">

            <label for="weight">Weight (lbs):</label>
            <input type="number" id="weight" name="weight">

            <label for="height">Height (inches):</label>
            <input type="number" id="height" name="height">

            <button type="submit">Update</button>
        </form>
    </div>

    <script src="assets/info.js"></script>  <!-- Link to script.js -->
</body>
</html>

