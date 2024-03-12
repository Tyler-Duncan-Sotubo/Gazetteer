<?php


$executionStartTime = microtime(true);

# this includes the login details
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

# Prepared statement to select location count
$query = $conn->prepare(
    'SELECT l.id, l.name, COUNT(d.locationID) AS location_count
    FROM location l
    LEFT JOIN department d ON d.locationID = l.id
    WHERE l.id = ?  GROUP BY l.id'
);
$query->bind_param("i", $_POST['id']);
$query->execute();

if (false === $query) {
    resHandler(400, "executed", "query failed", []);
}

$result = $query->get_result();

# Check for successful query and fetch data
$department = [];
while ($row = mysqli_fetch_assoc($result)) {
    array_push($department, $row);
}

# Check for successful query
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data'] = $department;

mysqli_close($conn);

echo json_encode($output);
