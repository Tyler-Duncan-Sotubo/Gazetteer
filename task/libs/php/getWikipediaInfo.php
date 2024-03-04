<?php

// remove for production

$executionStartTime = microtime(true);

$removeInputSpace = str_replace(' ', '+', $_REQUEST['q']);

$url = 'http://api.geonames.org/wikipediaSearchJSON?formatted=true&q=' . $removeInputSpace . '&maxRows=10' . '&username=tyleroftx&style=full';

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

curl_close($ch);

if (!$result) {
    $output['status']['code'] = "400";
    $output['status']['name'] = "Error";
    $output['status']['description'] = "Request failed";
    $output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['data'] = [];

    echo json_encode($output);
} else {
    $decode = json_decode($result, true);
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['data'] = $decode['geonames'];

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}
