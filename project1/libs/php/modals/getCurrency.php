<?php

require '../apiKeys.php';
require '../function.php';

// Set the endpoint
$endpoint = 'convert';
// initialize CURL:
$ch = curl_init('https://api.exchangeratesapi.io/v1/' . $endpoint . '?access_key=' . CURRENCY_API_KEY . '&from=' . $_REQUEST['from'] . '&to=' . $_REQUEST['to'] . '&amount=' . $_REQUEST['amount'] . '');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// get the JSON data:
$json = curl_exec($ch);
curl_close($ch);
// Decode JSON response:
$conversionResult = json_decode($json, true);
// access the conversion result

$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpStatusCode >= 400) {
    jsonHandler($httpStatusCode, 'Request failed', 'failed');
} else {
    jsonHandler(200, 'ok', 'success', $conversionResult, "result");
}
