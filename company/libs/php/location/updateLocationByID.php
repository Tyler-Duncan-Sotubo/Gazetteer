<?php


$executionStartTime = microtime(true);

include("../config.php");
include("../function.php");

header('Content-Type: application/json; charset=UTF-8');

# Create connection
$conn = new mysqli($servername, $username, $password, $database);

# Check connection
if (mysqli_connect_errno()) {
    resHandler(300, "failure", "database unavailable");
}

# Prepared statement to update location by id
$query = $conn->prepare('UPDATE location SET `name` = ? WHERE `id` = ?');
$query->bind_param("si", $name, $id);

# Set the values for the parameters
$name = $_POST['name'];
$id = $_POST['id'];

# Execute the query
$query->execute();

# Check for successful update
if ($query->affected_rows > 0) {
    resHandler(200, "success", "personnel updated");
} else {
    resHandler(400, "failure", "personnel not updated");
}
