<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in
?>

<!DOCTYPE html>
<html>
<head>
   <link rel="stylesheet" href="style.css">
   <title>Mission Critical</title>
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

<div class="hero-image">
    <div class="hero-text">
        <h1 style="font-size:64px;">Mission Critical</h1>
        <p style="font-size:32px;">It's a tough galaxy out there, but you can be tougher!</p>
        
        <?php if (!$isLoggedIn): ?>
        <a href="login.php">
            <button style="background-color:#0c094e; border-radius: 16px; padding: 10px 20px; font-size:20px; color:white; cursor: pointer;">
                Login
            </button>
        </a>
        <?php endif; ?>
    </div>
</div>

<footer style="background: #0f0a66; color:white; padding: 10px 20px;">
    <p>&copy; Copyright Mission Critical Group</p>
</footer>
</body>
</html>
