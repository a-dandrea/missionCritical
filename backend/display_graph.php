<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['year']) && isset($_GET['month'])) {
    $year = intval($_GET['year']);
    $month = intval($_GET['month']);

    if ($year < 1900 || $year > 2100 || $month < 1 || $month > 12) {
        echo "Error: Invalid year or month.";
        exit();
    }

    $command = escapeshellcmd("/usr/bin/python3 ../mc_basic_code/mc_weightGraph.py $year $month");
    $descriptor_spec = [
        0 => ["pipe", "r"],  // stdin
        1 => ["pipe", "w"],  // stdout
        2 => ["pipe", "w"]   // stderr
    ];

    $process = proc_open($command, $descriptor_spec, $pipes);

    if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]);
        $error_output = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $return_value = proc_close($process);

        error_log("Command: $command");
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
    echo "Error: Year and month are required.";
}
?>