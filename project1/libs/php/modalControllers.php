<?php

$requestData = $_REQUEST['action'];

switch ($requestData) {
    case 'currency':
        getCurrency();
        break;
    case 'weather':
        getWeather();
        break;
    case 'wikipedia':
        getWikipediaInfo();
        break;
    case 'news':
        getLatestNews();
        break;
    default:
        echo 'Invalid action';
}


function getCurrency()
{
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
}

function getWeather()
{
    $executionStartTime = microtime(true);


    $url = 'https://api.openweathermap.org/data/2.5/forecast/daily?q=' . $_REQUEST['city'] . '&units=metric&APPID=b6ea019473b1df46a1fa1dac301537dd';

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
    $output['data'] = $decode['list'];

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}


function getWikipediaInfo()
{
    $executionStartTime = microtime(true);

    $removeInputSpace = str_replace(' ', '+', $_REQUEST['q']);

    $url = 'http://api.geonames.org/wikipediaSearchJSON?formatted=true&q=' . $removeInputSpace . '&maxRows=3' . '&username=tyleroftx&style=full';

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
    $output['data'] = $decode['geonames'];

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}



function getLatestNews()

{

    $countryCode = $_REQUEST['countryCode'];
    $url = "https://newsapi.org/v2/top-headlines?country=" .   $countryCode . "&apiKey=57b6d593967e4fee9d23bc8717793dc8";
    $API_KEY = "57b6d593967e4fee9d23bc8717793dc8";

    $executionStartTime = microtime(true);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: ' . $API_KEY, // pass your key here
        'User-Agent: testing'
    ));

    $result = curl_exec($ch);

    curl_close($ch);

    $decode = json_decode($result, true);
    $articles = array_slice($decode['articles'], 0, 6);


    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['data'] = $articles;

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}
