<?php

# this includes the login details
include("../config.php");
include("../function.php");

header('Content-Type: application/json; charset=UTF-8');

# Create connection
$conn = new mysqli($servername, $username, $password, $database);

$executionStartTime = microtime(true);

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


# insert into department table name and location id
$query = $conn->prepare('INSERT INTO department (name, locationID) VALUES(?,?)');
$query->bind_param("si", $_REQUEST['name'], $_REQUEST['locationID']);
$query->execute();

# 
if (false === $query) {

    $output['status']['code'] = "400";
    $output['status']['name'] = "executed";
    $output['status']['description'] = "query failed";
    $output['data'] = [];

    mysqli_close($conn);

    echo json_encode($output);

    exit;
}

# Send reponse to client
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data'] = [];

# close the connection
mysqli_close($conn);

# encode the output as json
echo json_encode($output);
