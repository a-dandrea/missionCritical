<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';  // Use the correct database name
$username = 'ejerrier';  // Use the correct MySQL username
$password = '1788128';  // Use the correct MySQL password

try {
   $db = new PDO($dsn, $username, $password);
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   $error_message = $e->getMessage();
   exit("Database connection failed: " . $error_message);
}

$sql = "SELECT privilege FROM users WHERE user_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user_privilege = $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recipe Results</title>
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

        <?php if ($user_privilege == '2'): ?>
               <a href="childDashboard.php">Dashboard</a>
            <?php else: ?>
               <a href="dashboard.php">Dashboard</a>
            <?php endif; ?>
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
<h1 style="padding: 80px 10px 0px 10px;">Recipe Results</h1>

<?php
// Your existing PHP logic goes here (exactly as you have it)
if (isset($_GET['query'])) {
    $query = urlencode($_GET['query']);
    $diet = isset($_GET['diet']) ? $_GET['diet'] : '';
    $cuisine = isset($_GET['cuisine']) ? $_GET['cuisine'] : '';
    $intolerances = isset($_GET['intolerances']) ? urlencode($_GET['intolerances']) : '';
    $api_key = "069c7789fc754751abc1fe7e36f7e86a";

    $url = "https://api.spoonacular.com/recipes/complexSearch?query=$query&apiKey=$api_key&addRecipeInformation=true&addRecipeNutrition=true";
    if ($diet) $url .= "&diet=$diet";
    if ($cuisine) $url .= "&cuisine=$cuisine";
    if ($intolerances) $url .= "&intolerances=$intolerances";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        echo "Error fetching recipes: " . curl_error($ch);
    } else {
        $data = json_decode($response, true);

        if (isset($data['results']) && count($data['results']) > 0) {
            echo "<div class='recipe-grid'>"; // Changed from carousel to recipe-grid

            foreach ($data['results'] as $recipe) {
                echo "<div class='card'>";
                echo "<img src='" . htmlspecialchars($recipe['image']) . "' alt='" . htmlspecialchars($recipe['title']) . "'>";
                echo "<div class='card-content'>";
                echo "<h3>" . htmlspecialchars($recipe['title']) . "</h3>";
                echo "<p><strong>Servings:</strong> " . $recipe['servings'] . "</p>";
                echo "<p><strong>Ready in:</strong> " . $recipe['readyInMinutes'] . " minutes</p>";

                $nutrients = $recipe['nutrition']['nutrients'];
                $macros = ['Calories', 'Protein', 'Fat', 'Carbohydrates'];

                foreach ($macros as $macro) {
                    foreach ($nutrients as $n) {
                        if ($n['name'] === $macro) {
                            echo "<p><strong>$macro:</strong> " . $n['amount'] . " " . $n['unit'] . "</p>";
                            break;
                        }
                    }
                }

                echo "<a href='https://spoonacular.com/recipes/" . urlencode($recipe['title']) . "-" . $recipe['id'] . "' target='_blank'>View Recipe</a>";
                echo "</div>";
                echo "</div>";
            }

            echo "</div>"; // End of recipe grid
        } else {
            echo "No recipes found matching your criteria.";
        }
    }

    curl_close($ch);
} else {
    echo "Please enter a search term.";
}
?>
</div>
</body>

<footer style="background: #0f0a66; color:white; padding: 10px 20px;">
    <p>&copy; Copyright Mission Critical Group</p>
</footer>
</html>