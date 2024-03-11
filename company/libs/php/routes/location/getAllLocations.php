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

# Query Database for to select location
$query = 'SELECT id, name FROM location  
          ORDER BY name';

# Execute the query
$result = db($query);

# Check for successful query
if (!$result) {
    resHandler(400, "executed", "query failed", []);
}

# Fetch data
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    array_push($data, $row);
}


$found = [];
# Check if the user has entered a search term
if (isset($_POST['txt'])) {
    $query = $conn->prepare('SELECT `l`.`id`, `l`.`name` FROM `location` `l`  WHERE `l`.`name` LIKE ? 
    ORDER BY `l`.`name`');
    $likeText = "%" . $_POST['txt'] . "%";
    $query->bind_param("s", $likeText);

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

    $result = $query->get_result();
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($found, $row);
    }

    # If the user has entered a search term, return the search results
    $data = $found;
}

# Send success message and status to client
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data'] = $data;

# Close database connection
mysqli_close($conn);

# send output to client
echo json_encode($output);
