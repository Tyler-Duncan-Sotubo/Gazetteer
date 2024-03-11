<?php

require_once('../model/location.php');

if (isset($_POST['action']) && $_POST['action'] == 'create') {
    $location = new Location();
    $data =  $location->createLocation();
    echo $data;
}

if (isset($_POST['action']) && $_POST['action'] == 'read') {
    $location = new Location();
    $data = $location->readLocation();
    echo $data;
}

if (isset($_GET['action']) && $_GET['action'] == 'read') {
    $location = new Location();
    $data = $location->readLocation();
    echo $data;
}


if (isset($_POST['action']) && $_POST['action'] == 'readByID') {
    $location = new Location();
    $data = $location->readLocationByID();
    echo $data;
}

if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $location = new Location();
    $data = $location->updateLocation();
    echo $data;
}

if (isset($_POST['action']) && $_POST['action'] == 'count') {
    $location = new Location();
    $data = $location->readLocationCount();
    echo $data;
}

if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $location = new Location();
    $data = $location->deleteLocation();
    echo $data;
}
