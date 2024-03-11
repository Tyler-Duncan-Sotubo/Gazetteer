<?php

# this includes the login details
include("../config.php");
include("../function.php");

# this includes the login details
header('Content-Type: application/json; charset=UTF-8');

# this includes the login details
$conn = new mysqli($servername, $username, $password, $database);

$executionStartTime = microtime(true);


if (mysqli_connect_errno()) {
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
$query = $conn->prepare('INSERT INTO location (name) VALUES(?)');
$query->bind_param("s", $_POST['name']);
$query->execute();

if (false === $query) {

    $output['status']['code'] = "400";
    $output['status']['name'] = "executed";
    $output['status']['description'] = "query failed";
    $output['data'] = [];

    mysqli_close($conn);

    echo json_encode($output);

    exit;
}

# Send Success Response to client
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data'] = [];

# Close the connection
mysqli_close($conn);

# Send the output to the client
echo json_encode($output);
