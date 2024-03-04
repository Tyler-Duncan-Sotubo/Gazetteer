<?php

require '../function.php';

// Get the country code from the request
$_REQUEST['countryCode'];
// Concatenate the url with the country code and the api key
$url = 'http://api.geonames.org/searchJSON?q=university&country=' . $_REQUEST['countryCode']  . '&maxRows=10&username=tyleroftx';
// Initialize curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
// Execute the curl
$result = curl_exec($ch);
// Close the curl
curl_close($ch);
// Get the http status code
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
