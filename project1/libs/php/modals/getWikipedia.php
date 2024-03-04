<?php

require '../function.php';

// remove spaces from input
$removeInputSpace = str_replace(' ', '+', $_REQUEST['q']);
// wikipedia API endpoint
$url = 'http://api.geonames.org/wikipediaSearchJSON?formatted=true&q=' . $removeInputSpace . '&maxRows=10' . '&username=tyleroftx&style=full';
// Initialize curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
// Execute the curl
$result = curl_exec($ch);
curl_close($ch);
// Decode the result and send it to the response handler

$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Decode the result
$decode = json_decode($result, true);

// If the http status code is greater than or equal to 400, send a response with the error message
if ($httpStatusCode >= 400) {
    jsonHandler($httpStatusCode, 'Request failed', 'failed');
} else {
    // Send response to json handler
    jsonHandler(200, 'ok', 'success', $decode, 'geonames');
}
