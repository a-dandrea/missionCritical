<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recipe Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Recipe Results</h1>

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

</body>
</html>