<?php

$json = json_decode(file_get_contents("../../json/currency.json"), true);
// Sort the countries array by name
usort($json, function ($item1, $item2) {
    return $item1['country'] <=> $item2['country'];
});

// If the countries array is empty, send a response with the error message
if (count($json) === 0) {
    jsonHandler(400, 'Request failed', 'failed');
} else {
    $executionStartTime = microtime(true);
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['data'] = $json;

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}
