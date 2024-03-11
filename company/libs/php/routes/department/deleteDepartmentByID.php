<?php

$executionStartTime = microtime(true);

# this includes the login details
include("../config.php");
include("../function.php");

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

# SQL statement accepts parameters and so is prepared to avoid SQL injection.
$query = $conn->prepare('DELETE FROM department WHERE id = ?');
$query->bind_param("i", $_POST['id']);
$query->execute();

# Check for successful query
if (false === $query) {
	# If the query fails, return an error response
	$output['status']['code'] = "400";
	$output['status']['name'] = "executed";
	$output['status']['description'] = "query failed";
	$output['data'] = [];

	mysqli_close($conn);

	echo json_encode($output);

	exit;
}

# Send status and empty data to client
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data'] = [];

mysqli_close($conn);

echo json_encode($output);
