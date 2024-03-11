<?php

// remove next two lines for production
ini_set('display_errors', 'On');
error_reporting(E_ALL);


$executionStartTime = microtime(true);


include("../config.php");
include("../function.php");

header('Content-Type: application/json; charset=UTF-8');

$conn = new mysqli($servername, $username, $password, $database);

if (mysqli_connect_errno()) {
    resHandler(300, "failure", "database unavailable");
}

// Prepared statement to update department by id
$query = $conn->prepare('UPDATE department SET `name` = ?, `locationID` = ?  WHERE `id` = ?');
$query->bind_param("sii", $name, $locationID, $id);

// Set the values for the parameters
$name = $_POST['name'];
$locationID = $_POST['locationID'];
$id = $_POST['id'];

$query->execute();

if ($query->affected_rows > 0) {
    resHandler(200, "success", "personnel updated");
} else {
    resHandler(400, "failure", "personnel not updated");
}
