<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_log("PHP is running.");


session_start(); // Start session to get user ID

if (!isset($_SESSION['user_id'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in."]);
   exit();
}

$user_id = intval($_SESSION['user_id']); // Get logged-in user ID

if (isset($_GET['year']) && isset($_GET['month'])) {
   $year = intval($_GET['year']);
   $month = intval($_GET['month']);

   if ($year < 1900 || $year > 2100 || $month < 1 || $month > 12) {
      echo json_encode(["status" => "error", "message" => "Invalid year or month."]);
      exit();
   }

   $yearEscaped = escapeshellarg($year);
   $monthEscaped = escapeshellarg($month);
   $userIdEscaped = escapeshellarg($user_id);

   $weightCommand = "/usr/bin/python3 /home/students/adandrea/public_html/missionCritical/mc_basic_code/mc_weightGraph.py $year $month $user_id";
   $stepCommand = "/usr/bin/python3 /home/students/adandrea/public_html/missionCritical/mc_basic_code/mc_stepGraph.py $year $month $user_id";


   $descriptor_spec = [
      0 => ["pipe", "r"],  // stdin
      1 => ["pipe", "w"],  // stdout
      2 => ["pipe", "w"]   // stderr
   ];

   $env = [
      'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
      'HOME' => '/home/students/adandrea',
      'USER' => 'adandrea',
      'LANG' => 'en_US.UTF-8',
      'MPLCONFIGDIR' => '/tmp'
  ];

   $weightProcess = proc_open($weightCommand, $descriptor_spec, $pipes, null, $env);
   $stepProcess = proc_open($stepCommand, $descriptor_spec, $pipes, null, $env);

   if (is_resource($weightProcess)) {
      $output = stream_get_contents($pipes[1]);
      $error_output = stream_get_contents($pipes[2]);
      fclose($pipes[1]);
      fclose($pipes[2]);

      $return_value = proc_close($weightProcess);

      error_log("Command: $weightCommand");
      error_log("Output: $output");
      error_log("Error Output: $error_output");
      error_log("Return Value: $return_value");

      if ($return_value !== 0) {
         echo json_encode(["status" => "error", "message" => "Error executing Python script.", "error" => $error_output]);
         exit();
      }

      $image_path = trim($output);

      $image_url = str_replace("/home/students/adandrea/public_html", "/~adandrea", $image_path);

      if (!empty($image_path) && file_exists($image_path)) {
         echo json_encode(["status" => "success", "path" => $image_url]);
      } else {
         error_log("Error: Image not found at " . $image_path);
         echo json_encode(["status" => "error", "message" => "Error generating graph."]);
      }
   } else if (is_resource($stepProcess)) {
      $output = stream_get_contents($pipes[1]);
      $error_output = stream_get_contents($pipes[2]);
      fclose($pipes[1]);
      fclose($pipes[2]);

      $return_value = proc_close($stepProcess);

      error_log("Command: $stepCommand");
      error_log("Output: $output");
      error_log("Error Output: $error_output");
      error_log("Return Value: $return_value");

      if ($return_value !== 0) {
         echo json_encode(["status" => "error", "message" => "Error executing Python script.", "error" => $error_output]);
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
      echo json_encode(["status" => "error", "message" => "Failed to open process."]);
   }
} else {
   echo json_encode(["status" => "error", "message" => "Year and month are required."]);
}
?>
