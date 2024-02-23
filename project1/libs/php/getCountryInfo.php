<?php

// remove for production

$executionStartTime = microtime(true);

$countries = $_REQUEST['country'];

list($iso, $country) = explode(",", $countries);

$removeInputSpace = str_replace(' ', '+', $country);

$url = 'https://api.opencagedata.com/geocode/v1/json?q=' . $removeInputSpace . '&key=03354c19df174ebfb8e3f5995d41c8e6';

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
$output['data'] = $decode['results'];

header('Content-Type: application/json; charset=UTF-8');

echo json_encode($output);
