<?php

require './function.php';

$countryCode = $_REQUEST['countryCode'];

// Rest countries API endpoint
$url = 'https://restcountries.com/v3.1/alpha/' . $countryCode;
// Initialize curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
// Execute the curl
$result = curl_exec($ch);
// Close the curl
curl_close($ch);

$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Decode the result
$decode = json_decode($result, true);

// If the http status code is greater than or equal to 400, send a response with the error message
if ($httpStatusCode >= 400) {
    jsonHandler($httpStatusCode, 'Request failed', 'failed');
} else {
    // Send response to json handler
    $executionStartTime = microtime(true);
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['data'] = $decode;

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}
