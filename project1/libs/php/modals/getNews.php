<?php

require "../apiKeys.php";
require "../function.php";

// Get the country code from the request
$countryCode = $_REQUEST['countryCode'];
// News API endpoint

$url = "https://newsdata.io/api/1/news?country=" . $countryCode . "&size=6&prioritydomain=top&apikey=pub_393399ea38d46692e9c3abdfffd676bcf939d";
// Initialize curl 
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);

// Execute the curl
$result = curl_exec($ch);
// Close the curl
curl_close($ch);
// Decode the result
$decode = json_decode($result, true);

$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

$executionStartTime = microtime(true);
$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
$output['data'] = $decode['results'];

header('Content-Type: application/json; charset=UTF-8');

echo json_encode($output);
