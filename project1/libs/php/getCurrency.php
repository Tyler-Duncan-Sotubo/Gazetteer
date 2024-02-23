<?php
// set API Endpoint, access key, required parameters
$endpoint = 'convert';
$access_key = 'a9d2858b9a04b5286e8f1cab3d8cad28';

// initialize CURL:
$ch = curl_init('https://api.exchangeratesapi.io/v1/' . $endpoint . '?access_key=' . $access_key . '&from=' . $_REQUEST['from'] . '&to=' . $_REQUEST['to'] . '&amount=' . $_REQUEST['amount'] . '');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// get the JSON data:
$json = curl_exec($ch);
curl_close($ch);

// Decode JSON response:
$conversionResult = json_decode($json, true);

// access the conversion result
echo $conversionResult['result'];
