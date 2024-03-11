<?php

include("config.php");

$conn = new mysqli($servername, $username, $password, $database);

if (mysqli_connect_errno()) {
    $output['status']['code'] = "300";
    $output['status']['name'] = "failure";
    $output['status']['description'] = "database unavailable";
    $output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
    $output['data'] = [];

    mysqli_close($conn);

    echo json_encode($output);

    exit;
}

function db($query)
{
    global $conn;
    $result = $conn->query($query);
    if (!$result) {
        echo "Error executing query: ";
    }
    return $result;
}


function resHandler($code, $name, $desc, $data = null)
{
    global $conn;

    $executionStartTime = microtime(true);
    $output['status']['code'] = $code;
    $output['status']['name'] = $name;
    $output['status']['description'] = $desc;
    $output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
    $output['data'] = $data;

    $conn->close();

    echo json_encode($output);
    exit;
}
