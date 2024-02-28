<?php


declare(strict_types=1);

// Function to handle the response with string to the specified subData
function resHandler($data, string $subData = null)
{
    $executionStartTime = microtime(true);
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['data'] = $data[$subData];

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}

// Function to handle the response without string
function resJSON($data)
{
    $executionStartTime = microtime(true);
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    $output['data'] = $data;

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}
