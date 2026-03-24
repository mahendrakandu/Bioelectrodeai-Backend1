<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['dataset'])) {
    $fileTmpPath = $_FILES['dataset']['tmp_name'];
    
    // Execute Python script (assumes 'python' is in PATH)
    $command = "python ai_analysis.py " . escapeshellarg($fileTmpPath) . " 2>&1";
    $output = shell_exec($command);
    
    if ($output) {
        // Find the JSON part in the output in case there are warnings
        $jsonStart = strpos($output, '{');
        if ($jsonStart !== false) {
            $jsonResponse = substr($output, $jsonStart);
            echo $jsonResponse;
        } else {
            echo json_encode(["error" => "Invalid JSON from Python: " . $output]);
        }
    } else {
        echo json_encode(["error" => "Python script execution failed or empty output"]);
    }
} else {
    echo json_encode(["error" => "No file uploaded or invalid request method"]);
}
?>
