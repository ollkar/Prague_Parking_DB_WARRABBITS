<?php

class Database
{
    const SERVERNAME = "localhost";
    const USERNAME = "root";
    const PASSWORD = "";
    const DBNAME = "prague_parking2";

    public static function open() // Open connection to DB-server.
    {
        $connection = new mysqli(self::SERVERNAME, self::USERNAME,self::PASSWORD,self::DBNAME);

        if ($connection->connect_error) 
        {
            die("Connection failed: " . $connection->connect_error);
        }
        return $connection;
    }

    // INSERT ParkingSpot  - Eric
    public static function Insert_ParkingSpots($size)
    {
        $conn = self::open();

        for ($i=1; $i <= 20; $i++) 
        { 
            $stmt = $conn->prepare("INSERT INTO `parkingspot` (parkingspotid, spotsize) VALUES (?,?);");
            $stmt->bind_param("ii", $i, $size);

            if($stmt->execute())
            {
                $stmt->close();
            }
            else
            {
                $stmt->close();
                $conn->close();
                throw new mysqli_sql_exception("ERROR Could not INSERT");
            }
        }

        $conn->close();
        return "Inserted successfully";  // return $_SESSION['message'] ? to display message freely
    }
    
    //insert into Vehicle - Olle
    public static function InsertVehicle($vehicletypeid, $regnr)
    {
        $conn = self::open();

        $stmt = $conn->prepare("INSERT INTO `Vehicle`(VehicleTypeID, RegNr) VALUES(?,?);");
        $stmt->bind_param("ii", $vehicletypeid, $regnr);

        if ($stmt->execute()) {
          echo "Insert was successfull!";
        } 
        else {
          echo "Error: " . $stmt . "<br>" . $conn->error;
        }

        $conn->close();
    }
    
}
?>
