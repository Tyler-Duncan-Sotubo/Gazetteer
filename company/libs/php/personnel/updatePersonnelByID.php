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
    resHandler(300, "failure", "database unavailable");
}

# Prepare the SQL statement
$query = $conn->prepare('UPDATE personnel SET `firstName` = ?, `lastName` = ? , `email` = ?, `jobTitle` =? , `departmentID` = ? WHERE `id` = ?');
$query->bind_param("ssssii", $firstName, $lastName, $email, $jobTitle, $departmentID, $id);

# Set the values for the parameters
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$jobTitle = $_POST['jobTitle'];
$departmentID = $_POST['departmentID'];
$id = $_POST['id'];

# Execute the query
$query->execute();

# Check for successful insertion
if ($query->affected_rows > 0) {
    resHandler(200, "success", "personnel updated");
} else {
    resHandler(400, "failure", "personnel not updated");
}
