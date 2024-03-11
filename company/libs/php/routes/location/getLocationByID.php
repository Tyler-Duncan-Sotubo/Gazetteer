<?php

$executionStartTime = microtime(true);

include("../config.php");
include("../function.php");

# Set the response header to JSON
header('Content-Type: application/json; charset=UTF-8');

# Create connection
$conn = new mysqli($servername, $username, $password, $database);

# Check connection
if (mysqli_connect_errno()) {
    # If the connection fails, return an error response
    $output['status']['code'] = "300";
    $output['status']['name'] = "failure";
    $output['status']['description'] = "database unavailable";
    $output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
    $output['data'] = [];


    mysqli_close($conn);

    echo json_encode($output);

    exit;
}

# Prepared statement
$query = $conn->prepare('SELECT id, name FROM location WHERE id =  ?');
$query->bind_param("i", $_POST['id']);
$query->execute();

if (false === $query) {

    $output['status']['code'] = "400";
    $output['status']['name'] = "executed";
    $output['status']['description'] = "query failed";
    $output['data'] = [];

    echo json_encode($output);

    mysqli_close($conn);
    exit;
}

$result = $query->get_result();

if (!$result) {
    resHandler(400, "executed", "query failed", []);
}

# Fetch data
$location = [];
while ($row = mysqli_fetch_assoc($result)) {
    array_push($location, $row);
}

# Send success message and status to client
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data'] = $location;

# Close the connection
mysqli_close($conn);

# Send the output to the client
echo json_encode($output);
