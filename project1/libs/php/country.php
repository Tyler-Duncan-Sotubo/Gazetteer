<?php

require './apiKeys.php';
require './function.php';

$request_data = $_REQUEST['key']; // Accessing a specific parameter named 'key'

// Switch statement to determine which function to call
switch ($request_data) {
    case 'code':
        getCountryByCode();
        break;
    case 'coords':
        getCountryByCoords();
        break;
    case 'select':
        countrySelect();
        break;
    default:
        break;
}

function countrySelect()
{
    $json = json_decode(file_get_contents("../json/countryBorders.geo.json"), true);

    // countries array
    $countries = [];

    // Loop through the features array and push the country code and name to the countries array
    foreach ($json['features'] as $feature) {
        $country = null;
        $country['iso'] = $feature["properties"]['iso_a2'];
        $country['country'] = $feature["properties"]['name'];
        array_push($countries, $country);
    };

    // Sort the countries array by name
    usort($countries, function ($item1, $item2) {
        return $item1['name'] <=> $item2['name'];
    });
    // Send response to json handler
    resJSON($countries);
}

function getCountryByCoords()
{
    // Get the latitude and longitude from the request
    $lat = $_REQUEST['lat'];
    $lng = $_REQUEST['lng'];
    // Concatenate the url with the latitude, longitude and the api key
    $url = 'https://api.opencagedata.com/geocode/v1/json?q=' . $lat . "," . $lng . '&key=' . OPENCAGE_API_KEY;
    // Initialize curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    // Execute the curl
    $result = curl_exec($ch);
    // Close the curl
    curl_close($ch);
    // Decode the result
    $decode = json_decode($result, true);
    // Send response to json handler
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $executionStartTime = microtime(true);
    $output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['data'] = $decode['results'];

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}


function getCountryByCode()
{
    $countries = $_REQUEST['country'];

    // destructuring the country code and name
    list($iso, $country) = explode(",", $countries);

    // Replace the space with a plus sign
    $removeInputSpace = str_replace(' ', '+', $country);

    // Concatenate the url with the country name and the api key
    $url = 'https://api.opencagedata.com/geocode/v1/json?q=' . $removeInputSpace . '&key=' . OPENCAGE_API_KEY;

    // Initialize curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);

    // Execute the curl
    $result = curl_exec($ch);

    curl_close($ch);
    // Decode the result
    $decode = json_decode($result, true);

    // Send response to json handler
    resHandler($decode, 'results');
}
