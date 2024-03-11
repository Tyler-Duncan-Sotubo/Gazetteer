<?php


$executionStartTime = microtime(true);

include("../config.php");
include("../function.php");

# Set the response header to JSON
header('Content-Type: application/json; charset=UTF-8');

# Create connection 
$conn = new mysqli($servername, $username, $password, $database);

if (mysqli_connect_errno()) {
    resHandler(300, "failure", "database unavailable");
}

# Prepare the SQL statement
$query = $conn->prepare('DELETE FROM personnel WHERE id = ?');
$query->bind_param("i", $_POST['id']);

# Execute the query
$query->execute();

# Check for successful insertion
if ($query->affected_rows > 0) {
    resHandler(200, "success", "personnel deleted", []);
} else {
    resHandler(400, "failure", "personnel not deleted", []);
}
