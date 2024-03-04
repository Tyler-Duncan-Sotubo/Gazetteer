<?php

$executionStartTime = microtime(true);
// Get the Json file
$json = json_decode(file_get_contents("../json/countryBorders.geo.json"), true);
// Loop through the json file and return the border of the country
$border = null;
foreach ($json['features'] as $feature) {
    if ($feature["properties"]["iso_a2"] ==  $_REQUEST['countryCode']) {
        $border = $feature;
        break;
    }
};

if (!$border) {
    $output['status']['code'] = "404";
    $output['status']['name'] = "not found";
    $output['status']['description'];
    $output['border'] = [];

    echo json_encode($output);
} else {
    // Send the response to the response handler
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['executedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['border'] = $border;
    // Set the header to json
    header('Content-Type: application/json; charset=UTF-8');
    // Send the response
    echo json_encode($output);
}
