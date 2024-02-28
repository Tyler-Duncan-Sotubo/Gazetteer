<?php

require './apiKeys.php';
require './function.php';

// Get the action from the request
$requestData = $_REQUEST['action'];
// Switch statement to determine which function to call
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

// Function to get the currency
function getCurrency()
{
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
    echo $conversionResult['result'];
}

function getWeather()
{
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
    // Decode the result and send it to the response handler
    $decode = json_decode($result, true);
    resHandler($decode, 'list');
}

// get wikipedia info
function getWikipediaInfo()
{
    // remove spaces from input
    $removeInputSpace = str_replace(' ', '+', $_REQUEST['q']);
    // wikipedia API endpoint
    $url = 'http://api.geonames.org/wikipediaSearchJSON?formatted=true&q=' . $removeInputSpace . '&maxRows=3' . '&username=tyleroftx&style=full';
    // Initialize curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    // Execute the curl
    $result = curl_exec($ch);
    curl_close($ch);
    // Decode the result and send it to the response handler
    $decode = json_decode($result, true);
    resHandler($decode, 'geonames');
}

// get latest news
function getLatestNews()
{
    // Get the country code from the request
    $countryCode = $_REQUEST['countryCode'];
    // News API endpoint
    $url = "https://newsapi.org/v2/top-headlines?country=" .   $countryCode . "&apiKey=" . NEWS_API_KEY;
    // Initialize curl 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: ' . NEWS_API_KEY, // pass your key here
        'User-Agent: testing'
    ));
    // Execute the curl
    $result = curl_exec($ch);
    // Close the curl
    curl_close($ch);
    // Decode the result
    $decode = json_decode($result, true);
    // Get the first 6 articles
    $articles = array_slice($decode['articles'], 0, 6);
    // sending response to json handler function
    resJSON($articles);
}
