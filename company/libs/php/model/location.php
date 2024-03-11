<?php

include("../function.php"); # Include the 'function.php' file (which contains the 'db' function)

class Location
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

    public function createLocation()
    {
        # Prepare the SQL statement
        $query = 'INSERT INTO location (name) VALUES(?)';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $name);

        # Set the values for the parameters
        $name = $_POST['name'];

        # Execute the query
        $stmt->execute();

        if (false === $stmt) {
            resHandler(400, "executed", "query failed", []);
        }

        # Check for successful insertion
        if ($stmt->affected_rows > 0) {
            resHandler(200, "success", "Location Created Successful", []);
        } else {
            resHandler(400, "failure", "Location Not Created", []);
        }
    }

    public function readLocation(): array
    {
        # SQL statement
        $query = 'SELECT id, name FROM location ORDER BY name';
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
            $query = 'SELECT * FROM location WHERE name LIKE ? ORDER BY name';
            $stmt = $this->conn->prepare($query);

            $likeText = "%" . $_POST['txt'] . "%";
            $stmt->bind_param("s", $likeText);

            $stmt->execute();

            if (false === $stmt) {
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

    public function readLocationByID()
    {
        # Prepare the SQL statement
        $query = 'SELECT * FROM location WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        # Set the values for the parameters
        $id = $_POST['id'];

        # Execute the query
        $stmt->execute();

        # Check for successful query
        if (false === $stmt) {
            resHandler(400, "executed", "query failed", []);
        }

        $result = $stmt->get_result();

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }

        # Set the response data and status
        resHandler(200, "ok", "success", $data);
    }

    public function readLocationCount()
    {
        # Prepared statement to select location count
        $query = 'SELECT l.id, l.name, COUNT(d.locationID) AS location_count FROM location l LEFT JOIN department d ON d.locationID = l.id WHERE l.id = ?  GROUP BY l.id';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();

        if (false === $stmt) {
            resHandler(400, "executed", "query failed", []);
        }

        $result = $stmt->get_result();

        # Check for successful query and fetch data
        $department = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($department, $row);
        }

        # Check for successful query
        resHandler(200, "ok", "success", $department);
    }

    public function updateLocation()
    {
        # Prepare the SQL statement
        $query = 'UPDATE location SET name = ? WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $name, $id);

        # Set the values for the parameters
        $name = $_POST['name'];
        $id = $_POST['id'];

        # Execute the query
        $stmt->execute();

        # Check for successful query
        if (false === $stmt) {
            resHandler(400, "executed", "query failed", []);
        }

        # Check for successful update
        if ($stmt->affected_rows > 0) {
            # If the update is successful, return a success response
            resHandler(200, "success", "Location Updated Successful", []);
        } else {
            # If the update fails, return an error response
            resHandler(400, "failure", "Location Not Updated", []);
        }
    }

    public function deleteLocation()
    {
        # Prepare the SQL statement
        $query = 'DELETE FROM location WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        # Set the values for the parameters
        $id = $_POST['id'];

        # Execute the query
        $stmt->execute();

        # Check for successful query
        if (false === $stmt) {
            resHandler(400, "executed", "query failed", []);
        }

        # Check for successful deletion
        if ($stmt->affected_rows > 0) {
            # If the deletion is successful, return a success response
            resHandler(200, "success", "Location Deleted Successful", []);
        } else {
            # If the deletion fails, return an error response
            resHandler(400, "failure", "Location Not Deleted", []);
        }
    }
}
