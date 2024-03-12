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

# Query Statment
$query = 'SELECT p.id, p.lastName, p.firstName, p.jobTitle, p.email, d.name as department, 
	l.name as location FROM personnel p LEFT JOIN department d ON (d.id = p.departmentID) 
	LEFT JOIN location l ON (l.id = d.locationID) ORDER BY p.lastName, p.firstName, d.name, l.name';

# Get Result
$result = db($query);

# Check for successful insertion
if (!$result) {
	resHandler(400, "executed", "query failed", []);
}

# Fetch Data
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
	array_push($data, $row);
}

# Search Query For Personnel
$found = [];

# Check if the txt parameter is set
if (isset($_POST['txt'])) {
	# Prepare the SQL statement
	$query = $conn->prepare('SELECT `p`.`id`, `p`.`firstName`, `p`.`lastName`, `p`.`email`, `p`.`jobTitle`, `d`.`id` as `departmentID`, `d`.`name` AS `department`, `l`.`id` as `locationID`, `l`.`name` AS `location` FROM `personnel` `p` LEFT JOIN `department` `d` ON (`d`.`id` = `p`.`departmentID`) LEFT JOIN `location` `l` ON (`l`.`id` = `d`.`locationID`) WHERE `p`.`firstName` LIKE ? OR `p`.`lastName` LIKE ? OR `p`.`email` LIKE ? OR `p`.`jobTitle` LIKE ? OR `d`.`name` LIKE ? OR `l`.`name` LIKE ? ORDER BY `p`.`lastName`, `p`.`firstName`, `d`.`name`, `l`.`name`');
	$likeText = "%" . $_POST['txt'] . "%";
	$query->bind_param("ssssss", $likeText, $likeText, $likeText, $likeText, $likeText, $likeText);

	$query->execute();

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

	# Get Result
	$result = $query->get_result();
	while ($row = mysqli_fetch_assoc($result)) {
		array_push($found, $row);
	}

	# Set the data to the found array
	$data = $found;
}

# Set the response data and status
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data'] = $data;

# close database connection
mysqli_close($conn);

# Return the output as JSON
echo json_encode($output);
