<?php


declare(strict_types=1);

function jsonHandler(int $errorCode, string $name, string $description, $data = null, string $string = null)
{

    $output['status']['code'] = $errorCode;
    $output['status']['name'] = $name;
    $output['status']['description'] = $description;
    $executionStartTime = microtime(true);
    $output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
    $output['data'] = $data[$string];


    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($output);
}
