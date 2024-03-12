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

# SQL statement
$query = 'SELECT d.id, d.name, d.locationID, l.name as location 
          FROM department d 
          LEFT JOIN location l ON (l.id = d.locationID) 
          ORDER BY d.name';
$result = db($query);

# Check for successful query
if (!$result) {
	# If the query fails, return an error response
	resHandler(400, "executed", "query failed", []);
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
	array_push($data, $row);
}

$found = [];

# Check if the user has entered a search term
if (isset($_POST['txt'])) {
	$query = $conn->prepare('SELECT `d`.`id`, `d`.`name`, `l`.`id` as `locationID`, `l`.`name` AS `location` 
	FROM `department` `d`
	LEFT JOIN `location` `l` ON (`l`.`id` = `d`.`locationID`) 
	WHERE `d`.`name` LIKE ? OR `l`.`name` LIKE ? 
	ORDER BY  `d`.`name`, `l`.`name`');

	$likeText = "%" . $_POST['txt'] . "%";
	$query->bind_param("ss", $likeText, $likeText);

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

	$result = $query->get_result();
	while ($row = mysqli_fetch_assoc($result)) {
		array_push($found, $row);
	}

	# If the search term is found in the database, return the data
	$data = $found;
}

# Send status and data to client
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data'] = $data;

mysqli_close($conn);

echo json_encode($output);
