<?php

include("../function.php");

class Department
{
    private $conn;

    public function __construct()
    {
        include("../config.php");
        $this->conn = new mysqli($servername, $username, $password, $database);
        if (mysqli_connect_errno()) {
            # If the connection fails, return an error response
            resHandler(300, "failure", "Error Retrieving Data");
        }
    }

    public function createDepartment()
    {
        # Prepare the SQL statement
        $query = 'INSERT INTO department (name, locationID) VALUES(?,?)';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $name, $locationID);

        # Set the values for the parameters
        $name = $_POST['name'];
        $locationID = $_POST['locationID'];

        # Execute the query
        $stmt->execute();

        # Check for successful insertion
        if ($stmt->affected_rows > 0) {
            resHandler(200, "success", "Department Created Successful", []);
        } else {
            resHandler(400, "failure", "Department Not Created", []);
        }
    }

    public function readDepartment(): array
    {
        # SQL statement
        $query = 'SELECT d.id, d.name, d.locationID, l.name as location FROM department d  LEFT JOIN location l ON (l.id = d.locationID)  ORDER BY d.name';
        $result = $this->conn->query($query);
        # Check for successful query
        if (!$result) {
            # If the query fails, return an error response
            resHandler(400, "executed", "query failed", []);
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }

        $found = [];
        # Check if the user has entered a search term
        if (isset($_POST['txt'])) {
            # Prepare the SQL statement
            $query = 'SELECT `d`.`id`, `d`.`name`, `l`.`id` as `locationID`, `l`.`name` AS `location` FROM `department` `d` LEFT JOIN `location` `l` ON (`l`.`id` = `d`.`locationID`)  WHERE `d`.`name` LIKE ? OR `l`.`name` LIKE ? ORDER BY  `d`.`name`, `l`.`name`';
            $stmt = $this->conn->prepare($query);

            $likeText = "%" . $_POST['txt'] . "%";
            $stmt->bind_param("ss", $likeText, $likeText);

            $stmt->execute();

            if (!$result) {
                resHandler(400, "executed", "query failed", []);
            }

            $result = $stmt->get_result();
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($found, $row);
            }
            # If the search term is found in the database, return the data
            $data = $found;
        }

        # Set the response data and status
        resHandler(200, "ok", "success", $data);
    }

    public function readDepartmentByID()
    {
        $executionStartTime = microtime(true);

        # SQL statement accepts parameters and so is prepared to avoid SQL injection.
        $query = 'SELECT id, name, locationID FROM department WHERE id =  ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();

        # Check for successful query
        if (false === $stmt) {
            # If the query fails, return an error response
            resHandler(400, "executed", "query failed", []);
        }
        $result = $stmt->get_result();

        # Check for successful query 
        $department = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($department, $row);
        }

        # SQL statement for location table
        $query = 'SELECT id, name FROM location ORDER BY name';
        $result = $this->conn->query($query);

        # Check for successful query
        if (!$result) {
            resHandler(400, "executed", "query failed", []);
        }

        # Fetch the result into an array
        $location = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($location, $row);
        }

        # Send status and data to client
        $output['status']['code'] = "200";
        $output['status']['name'] = "ok";
        $output['status']['description'] = "success";
        $output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
        $output['data']['location'] = $location;
        $output['data']['department'] = $department;

        mysqli_close($this->conn);

        echo json_encode($output);
    }

    public function readDepartmentCount(): array
    {
        // SQL statement accepts parameters and so is prepared to avoid SQL injection.
        $query = 'SELECT d.id, d.name, COUNT(p.departmentID) AS personnel_count FROM department d LEFT JOIN personnel p ON d.id = p.departmentID WHERE d.id = ? GROUP BY d.id';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();

        if (false === $query) {
            resHandler(400, "executed", "query failed", []);
        }

        $result = $stmt->get_result();

        $department = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($department, $row);
        }
        resHandler(200, "ok", "success", $department);
    }

    public function updateDepartment(): void
    {
        # Prepare the SQL statement
        $query = 'UPDATE department SET name = ?, locationID = ? WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sii", $name, $locationID, $id);

        # Set the values for the parameters
        $name = $_POST['name'];
        $locationID = $_POST['locationID'];
        $id = $_POST['id'];

        # Execute the query
        $stmt->execute();

        # Check for successful update
        if ($stmt->affected_rows > 0) {
            resHandler(200, "success", "Department Updated Successful", []);
        } else {
            resHandler(400, "failure", "Department Not Updated", []);
        }
    }

    public function deleteDepartment(): void
    {
        # Prepare the SQL statement
        $query = 'DELETE FROM department WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        # Set the values for the parameters
        $id = $_POST['id'];

        # Execute the query
        $stmt->execute();

        # Check for successful query
        if (false === $query) {
            # If the query fails, return an error response
            resHandler(400, "executed", "query failed", []);
        }

        # Check for successful deletion
        if ($stmt->affected_rows > 0) {
            resHandler(200, "success", "Department Deleted Successful", []);
        } else {
            resHandler(400, "failure", "Department Not Deleted", []);
        }
    }
}
