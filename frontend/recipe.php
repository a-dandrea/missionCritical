<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Search</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Recipe Search</h1>

    <form action="search.php" method="get">
        <!-- Recipe/Ingredient Search -->
        <label for="query">Search for a recipe or ingredient:</label>
        <input type="text" id="query" name="query" required>
        
        <br>

        <!-- Diet -->
        <label for="diet">Dietary Preferences:</label>
        <select id="diet" name="diet">
            <option value="">None</option>
            <option value="vegetarian">Vegetarian</option>
            <option value="vegan">Vegan</option>
            <option value="glutenFree">Gluten Free</option>
            <option value="ketogenic">Ketogenic</option>
        </select>
        
        <br>

        <!-- Cuisine -->
        <label for="cuisine">Cuisine:</label>
        <select id="cuisine" name="cuisine">
            <option value="">Any</option>
            <option value="Italian">Italian</option>
            <option value="Mexican">Mexican</option>
            <option value="Indian">Indian</option>
            <option value="Chinese">Chinese</option>
        </select>
        
        <br>

        <!-- Intolerances -->
        <label for="intolerances">Intolerances:</label>
        <input type="text" id="intolerances" name="intolerances" placeholder="e.g., dairy, peanut">
        
        <br>

        <!-- Submit Button -->
        <button type="submit">Search</button>
    </form>

</body>
</html>