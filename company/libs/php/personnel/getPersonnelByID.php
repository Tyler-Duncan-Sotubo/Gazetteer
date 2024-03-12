<?php

# Set the response header to JSON
$executionStartTime = microtime(true);


include("../config.php");
include("../function.php");

# Set the response header to JSON
header('Content-Type: application/json; charset=UTF-8');

# Create connection
$conn = new mysqli($servername, $username, $password, $database);

# Check connection
if (mysqli_connect_errno()) {
	resHandler(300, "failure", "database unavailable");
}


# first query
$query = $conn->prepare('SELECT `id`, `firstName`, `lastName`, `email`, `jobTitle`, `departmentID` FROM `personnel` WHERE `id` = ?');
$query->bind_param("i", $_POST['id']);
$query->execute();

// check response
if (false === $query) {
	resHandler(400, "executed", "query failed", []);
}

# get result
$result = $query->get_result();
# fetch data
$personnel = [];
while ($row = mysqli_fetch_assoc($result)) {
	array_push($personnel, $row);
}

# second query - does not accept parameters and so is not prepared
$query = 'SELECT id, name from department ORDER BY name';
$result = $conn->query($query);

# check if result
if (!$result) {
	resHandler(400, "executed", "query failed", []);
}

# fetch data
$department = [];
while ($row = mysqli_fetch_assoc($result)) {
	# push data to array
	array_push($department, $row);
}

# return response to client
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data']['personnel'] = $personnel;
$output['data']['department'] = $department;

mysqli_close($conn);

echo json_encode($output);
