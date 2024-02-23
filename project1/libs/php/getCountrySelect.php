<?php

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
