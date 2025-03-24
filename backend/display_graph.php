<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
   $command = "/usr/local/bin/python3 ../mc_basic_code/mc_weightGraph.py $year $month 2>&1";

   error_log("Executing command: " . $command);

   $output = shell_exec($command);
   $exitCode = shell_exec("echo $?"); // Capture the exit code

   error_log("Shell Output: " . var_export($output, true));
   error_log("Exit Code: " . trim($exitCode));

   if ($output === null) {
      echo json_encode(["status" => "error", "message" => "shell_exec failed."]);
      exit();
   }

   $image_path = trim($output);

   if (!empty($image_path) && file_exists($image_path)) {
      echo json_encode(["status" => "success", "path" => $image_path]);
   } else { 
      error_log("Error: Image not found at " . $image_path);
      echo json_encode(["status" => "error", "message" => "Error generating graph."]);
   }
} else {
    echo "Error: Year and month are required.";
}
?>
