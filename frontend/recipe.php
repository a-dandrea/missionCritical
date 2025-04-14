<?php
   session_start();     
     $isLoggedIn = isset($_SESSION['user_id']);

     $dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
     $username = 'ejerrier';
     $password = '1788128';
   
     if (!$isLoggedIn) {
       header("Location: login.php");
       exit();
     }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Search</title>
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
    <h2>Recipe Search</h2>

    <form action="search.php" method="get">
        <!-- Recipe/Ingredient Search -->
        <label for="query" style="text-align: center;">Search for a recipe or ingredient:</label>
        
        <input type="text" id="query" name="query" required>
        
        <br>

        <!-- Diet -->
        <label for="diet" style="text-align: center;">Dietary Preferences:</label>
        <select id="diet" name="diet">
            <option value="">None</option>
            <option value="vegetarian">Vegetarian</option>
            <option value="vegan">Vegan</option>
            <option value="glutenFree">Gluten Free</option>
            <option value="ketogenic">Ketogenic</option>
        </select>
        
        <br>

        <!-- Cuisine -->
        <label for="cuisine" style="text-align: center;">Cuisine:</label>
        <select id="cuisine" name="cuisine">
            <option value="">Any</option>
            <option value="Italian">Italian</option>
            <option value="Mexican">Mexican</option>
            <option value="Indian">Indian</option>
            <option value="Chinese">Chinese</option>
        </select>
        
        <br>

        <!-- Intolerances -->
        <label for="intolerances" style="text-align: center;">Intolerances:</label>
        <input type="text" id="intolerances" name="intolerances" placeholder="e.g., dairy, peanut">
        
        <br>

        <!-- Submit Button -->
        <button type="submit">Search</button>
    </form>
</div>

</body>

<footer style="background: #0f0a66; color:white; padding: 10px 20px;">
    <p>&copy; Copyright Mission Critical Group</p>
</footer>
</html>