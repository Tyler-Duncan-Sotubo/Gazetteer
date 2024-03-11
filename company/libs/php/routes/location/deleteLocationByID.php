<?php


$executionStartTime = microtime(true);

# this includes the login details
include("../config.php");
include("../function.php");

# this includes the login details
header('Content-Type: application/json; charset=UTF-8');

# this includes the login details
$conn = new mysqli($servername, $username, $password, $database);

# check connection
if (mysqli_connect_errno()) {

    # If Error send status code 300
    $output['status']['code'] = "300";
    $output['status']['name'] = "failure";
    $output['status']['description'] = "database unavailable";
    $output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
    $output['data'] = [];

    mysqli_close($conn);

    echo json_encode($output);

    exit;
}

# Prepared Statement
$query = $conn->prepare('DELETE FROM location WHERE id = ?');
$query->bind_param("i", $_POST['id']);
$query->execute();

# Check for successful insertion
if (false === $query) {
    # If Error send status code 400
    $output['status']['code'] = "400";
    $output['status']['name'] = "executed";
    $output['status']['description'] = "query failed";
    $output['data'] = [];

    mysqli_close($conn);

    echo json_encode($output);

    exit;
}

# Send success message and status to client
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data'] = [];

# close connection
mysqli_close($conn);

# Send the output to the client
echo json_encode($output);
