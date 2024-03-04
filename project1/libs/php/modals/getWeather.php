<?php

require '../apiKeys.php';
require '../function.php';

// OpenWeatherMap API endpoint
$url = 'https://api.openweathermap.org/data/2.5/forecast/daily?q=' . $_REQUEST['city'] . '&units=metric&APPID=' . WEATHER_API_KEY;
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
    jsonHandler(200, 'ok', 'success', $decode, 'list');
}
