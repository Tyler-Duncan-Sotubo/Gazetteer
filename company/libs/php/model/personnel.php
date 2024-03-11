<?php

include("../function.php"); # Include the 'function.php' file (which contains the 'db' function)

class Personnel
{
    private $conn; # Declare the '$conn' property
    public function __construct()
    {
        include("../config.php");
        $this->conn = new mysqli($servername, $username, $password, $database);
        if (mysqli_connect_errno()) {
            # If the connection fails, return an error response
            resHandler(300, "failure", "database unavailable");
        }
    }

    public function createPersonnel(): void
    {
        # Prepare the SQL statement
        $query = 'INSERT INTO personnel (firstName, lastName, email, jobTitle, departmentID) VALUES(?,?,?,?,?)';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssi", $firstName, $lastName, $email, $jobTitle, $departmentID);

        # Set the values for the parameters
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $jobTitle = $_POST['jobTitle'];
        $departmentID = $_POST['departmentID'];

        # Execute the query
        $stmt->execute();

        # Check for successful insertion
        if ($stmt->affected_rows > 0) {
            resHandler(200, "success", "Personnel Created Successful", []);
        } else {
            resHandler(400, "failure", "Personnel Not Created", []);
        }
    }

    public function readPersonnel(): array
    {

        $query = 'SELECT p.lastName, p.firstName, p.jobTitle, p.email, d.name as department, l.name as location FROM personnel p LEFT JOIN department d ON (d.id = p.departmentID) LEFT JOIN location l ON (l.id = d.locationID) ORDER BY p.lastName, p.firstName, d.name, l.name';
        $result = $this->conn->query($query);

        if (!$result) {
            resHandler(400, "executed", "query failed", []);
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        # Search Query For Personnel
        $found = [];

        # Check if the txt parameter is set
        if (isset($_POST['txt'])) {
            # Prepare the SQL statement
            $query = 'SELECT `p`.`id`, `p`.`firstName`, `p`.`lastName`, `p`.`email`, `p`.`jobTitle`, `d`.`id` as `departmentID`, `d`.`name` AS `department`, `l`.`id` as `locationID`, `l`.`name` AS `location` FROM `personnel` `p` LEFT JOIN `department` `d` ON (`d`.`id` = `p`.`departmentID`) LEFT JOIN `location` `l` ON (`l`.`id` = `d`.`locationID`) WHERE `p`.`firstName` LIKE ? OR `p`.`lastName` LIKE ? OR `p`.`email` LIKE ? OR `p`.`jobTitle` LIKE ? OR `d`.`name` LIKE ? OR `l`.`name` LIKE ? ORDER BY `p`.`lastName`, `p`.`firstName`, `d`.`name`, `l`.`name`';
            $stmt = $this->conn->prepare('SELECT `p`.`id`, `p`.`firstName`, `p`.`lastName`, `p`.`email`, `p`.`jobTitle`, `d`.`id` as `departmentID`, `d`.`name` AS `department`, `l`.`id` as `locationID`, `l`.`name` AS `location` FROM `personnel` `p` LEFT JOIN `department` `d` ON (`d`.`id` = `p`.`departmentID`) LEFT JOIN `location` `l` ON (`l`.`id` = `d`.`locationID`) WHERE `p`.`firstName` LIKE ? OR `p`.`lastName` LIKE ? OR `p`.`email` LIKE ? OR `p`.`jobTitle` LIKE ? OR `d`.`name` LIKE ? OR `l`.`name` LIKE ? ORDER BY `p`.`lastName`, `p`.`firstName`, `d`.`name`, `l`.`name`');
            $likeText = "%" . $_POST['txt'] . "%";
            $stmt->bind_param("ssssss", $likeText, $likeText, $likeText, $likeText, $likeText, $likeText);

            $stmt->execute();

            if (false === $stmt) {
                # If the query fails, return an error response
                resHandler(400, "executed", "query failed", []);
            }

            # Get Result
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

    public function readPersonnelByID()
    {
        $executionStartTime = microtime(true);
        # first query
        $query = 'SELECT `id`, `firstName`, `lastName`, `email`, `jobTitle`, `departmentID` FROM `personnel` WHERE `id` = ?';
        # prepare the query
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $_POST['id']);
        # execute the query
        $stmt->execute();
        # check response
        if (false === $stmt) {
            resHandler(400, "executed", "query failed", []);
        }
        # get result
        $result = $stmt->get_result();
        # fetch data
        $personnel = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($personnel, $row);
        }

        # second query - does not accept parameters and so is not prepared
        $query = 'SELECT id, name from department ORDER BY name';
        $result = $this->conn->query($query);

        # check if result
        if (!$result) {
            resHandler(400, "executed", "query failed", []);
        }
        # fetch data
        $department = [];
        while ($row = mysqli_fetch_assoc($result)) {
            # push data to array
            array_push($department, $row);
        }

        # return response to client
        $output['status']['code'] = "200";
        $output['status']['name'] = "ok";
        $output['status']['description'] = "success";
        $output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
        $output['data']['personnel'] = $personnel;
        $output['data']['department'] = $department;

        mysqli_close($this->conn);

        echo json_encode($output);
    }

    public function updatePersonnel(): void
    {
        # Prepare the SQL statement
        $query = 'UPDATE personnel SET firstName = ?, lastName = ?, email = ?, jobTitle = ?, departmentID = ? WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssii", $firstName, $lastName, $email, $jobTitle, $departmentID, $id);

        # Set the values for the parameters
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $jobTitle = $_POST['jobTitle'];
        $departmentID = $_POST['departmentID'];
        $id = $_POST['id'];

        # Execute the query
        $stmt->execute();

        # Check for successful update
        if ($stmt->affected_rows > 0) {
            resHandler(200, "success", "Personnel Updated Successful", []);
        } else {
            resHandler(400, "failure", "Personnel Not Updated", []);
        }
    }

    public function deletePersonnelByID(): void
    {
        // Prepare the SQL statement 
        $query = 'DELETE FROM personnel WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();

        // Check for successful deletion 
        if ($stmt->affected_rows > 0) {
            resHandler(200, "success", "Personnel Deleted Successful", []);
        } else {
            resHandler(400, "failure", "Personnel Not Deleted", []);
        }
    }
}
