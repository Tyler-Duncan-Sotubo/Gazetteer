<?php

require_once('../model/personnel.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');

    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        $personnel = new Personnel();
        $data = $personnel->createPersonnel();
        echo $data;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'read') {
        $personnel = new Personnel();
        $data = $personnel->readPersonnel();
        echo $data;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'readByID') {
        $personnel = new Personnel();
        $data = $personnel->readPersonnelByID();
        echo $data;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $personnel = new Personnel();
        $data = $personnel->deletePersonnelByID();
        echo $data;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $personnel = new Personnel();
        $data = $personnel->updatePersonnel();
        echo $data;
    }
}
