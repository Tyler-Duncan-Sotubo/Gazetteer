<?php

$requestData = $_REQUEST['action'];
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

function getCities()
{
    $executionStartTime = microtime(true);
    $_REQUEST['countryCode'];

    $url = 'http://api.geonames.org/searchJSON?username=tyleroftx&country=' . $_REQUEST['countryCode']  . '&maxRows=15' . '&style=SHORT';

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
    $output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['data'] = $decode['geonames'];

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}


function getAirports()
{
    $executionStartTime = microtime(true);
    $_REQUEST['countryCode'];

    $url = 'http://api.geonames.org/searchJSON?q=airport&country=' . $_REQUEST['countryCode']  . '&maxRows=10&username=tyleroftx&style=short';

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
    $output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['data'] = $decode['geonames'];

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}


function getBorders()
{
    $executionStartTime = microtime(true);
    $json = json_decode(file_get_contents("../json/countryBorders.geo.json"), true);

    $border = null;
    foreach ($json['features'] as $feature) {
        if ($feature["properties"]["iso_a2"] ==  $_REQUEST['countryCode']) {
            $border = $feature;
            break;
        }
    };

    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['executedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['border'] = $border;

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}
