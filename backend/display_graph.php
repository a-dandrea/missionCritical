<?php
// Check if year and month parameters are set in the URL
if (isset($_GET['year']) && isset($_GET['month'])) {
    $year = intval($_GET['year']);   // Sanitize input to ensure it's an integer
    $month = intval($_GET['month']); // Sanitize input to ensure it's an integer

    // Validate inputs
    if ($year < 1900 || $year > 2100 || $month < 1 || $month > 12) {
        echo "Error: Invalid year or month.";
        exit();
    }

    // Run the Python script and capture the output (image path)
    $command = escapeshellcmd("python3 /path/to/generate_graph.py $year $month");
    $image_path = trim(shell_exec($command));

    // Check if the script executed successfully and the image exists
    if (!empty($image_path) && file_exists($image_path)) {
        echo "<img src='graph.png?" . time() . "' alt='Weight Graph'>"; // Add timestamp to prevent caching
    } else {
        echo "Error generating graph.";
    }
} else {
    echo "Error: Year and month are required.";
}
?>
