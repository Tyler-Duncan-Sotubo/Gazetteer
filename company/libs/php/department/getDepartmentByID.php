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

# SQL statement accepts parameters and so is prepared to avoid SQL injection.
$query = $conn->prepare('SELECT id, name, locationID FROM department WHERE id =  ?');
$query->bind_param("i", $_POST['id']);
$query->execute();

# Check for successful query
if (false === $query) {
	# If the query fails, return an error response
	$output['status']['code'] = "400";
	$output['status']['name'] = "executed";
	$output['status']['description'] = "query failed";
	$output['data'] = [];

	echo json_encode($output);

	mysqli_close($conn);
	exit;
}

$result = $query->get_result();

# Check for successful query 
$department = [];
while ($row = mysqli_fetch_assoc($result)) {
	array_push($department, $row);
}

# SQL statement for location table
$query = 'SELECT id, name FROM location ORDER BY name';
$result = $conn->query($query);

# Check for successful query
if (!$result) {
	resHandler(400, "executed", "query failed", []);
}

# Fetch the result into an array
$location = [];
while ($row = mysqli_fetch_assoc($result)) {
	array_push($location, $row);
}

# Send status and data to client
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data']['location'] = $location;
$output['data']['department'] = $department;

mysqli_close($conn);

echo json_encode($output);
