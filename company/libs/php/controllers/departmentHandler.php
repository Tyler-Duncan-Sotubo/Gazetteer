<?php

require_once('../model/department.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');

    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        $department = new Department();
        $data = $department->createDepartment();
        echo $data;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'read') {
        $department = new Department();
        $data = $department->readDepartment();
        echo $data;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'readByID') {
        $department = new Department();
        $data = $department->readDepartmentByID();
        echo $data;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $department = new Department();
        $data = $department->updateDepartment();
        echo $data;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'count') {
        $department = new Department();
        $data = $department->readDepartmentCount();
        echo $data;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $department = new Department();
        $data = $department->deleteDepartment();
        echo $data;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    header('Content-Type: application/json');
    if (isset($_GET['action']) && $_GET['action'] == 'read') {
        $department = new Department();
        $data = $department->readDepartment();
        echo $data;
    }
}
