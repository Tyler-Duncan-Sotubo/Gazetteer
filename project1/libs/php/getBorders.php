<?php

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
