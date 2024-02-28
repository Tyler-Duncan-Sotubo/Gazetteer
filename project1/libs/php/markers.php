<?php
require './function.php';

// Get the action from the request
$requestData = $_REQUEST['action'];
// Switch statement to determine which function to call
switch ($requestData) {
    case 'city':
        getCities();
        break;
    case 'airport':
        getAirports();
        break;
    case 'borders':
        getBorders();
        break;
    default:
        echo 'Invalid action';
}

// Function to get the cities
function getCities()
{
    // Get the country code from the request
    $_REQUEST['countryCode'];
    // Concatenate the url with the country code and the api key
    $url = 'http://api.geonames.org/searchJSON?username=tyleroftx&country=' . $_REQUEST['countryCode']  . '&maxRows=15' . '&style=SHORT';
    // Initialize curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    // Execute the curl
    $result = curl_exec($ch);
    // Close the curl
    curl_close($ch);
    // Decode the result and send it to the response handler
    $decode = json_decode($result, true);
    resHandler($decode, 'geonames');
}


function getAirports()
{
    // Get the country code from the request
    $_REQUEST['countryCode'];
    // Concatenate the url with the country code and the api key
    $url = 'http://api.geonames.org/searchJSON?q=airport&country=' . $_REQUEST['countryCode']  . '&maxRows=10&username=tyleroftx&style=short';
    // Initialize curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    // Execute the curl
    $result = curl_exec($ch);
    // Close the curl
    curl_close($ch);
    // Decode the result and send it to the response handler
    $decode = json_decode($result, true);
    // Send the response to the response handler
    resHandler($decode, 'geonames');
}


function getBorders()
{
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
