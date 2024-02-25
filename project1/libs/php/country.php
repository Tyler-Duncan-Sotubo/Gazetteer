<?php

$request_data = $_REQUEST['key']; // Accessing a specific parameter named 'key'
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
    $executionStartTime = microtime(true);
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

    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['executedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['data'] = $countries;

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}

function getCountryByCoords()
{
    $lat = $_REQUEST['lat'];
    $lng = $_REQUEST['lng'];

    $url = 'https://api.opencagedata.com/geocode/v1/json?q=' . $lat . "," . $lng . '&key=03354c19df174ebfb8e3f5995d41c8e6';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);

    $result = curl_exec($ch);

    curl_close($ch);

    $decode = json_decode($result, true);

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

    list($iso, $country) = explode(",", $countries);

    $removeInputSpace = str_replace(' ', '+', $country);

    $url = 'https://api.opencagedata.com/geocode/v1/json?q=' . $removeInputSpace . '&key=03354c19df174ebfb8e3f5995d41c8e6';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);

    $result = curl_exec($ch);

    curl_close($ch);

    $decode = json_decode($result, true);

    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $executionStartTime = microtime(true);
    $output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['data'] = $decode['results'];

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}
