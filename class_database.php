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
    public static function Insert_Vehicle($vehicletypeid, $regnr)
    {
        $conn = self::open();

        $stmt = $conn->prepare("INSERT INTO `Vehicle`(VehicleTypeID, RegNr) VALUES(?,?);");
        $stmt->bind_param("is", $vehicletypeid, $regnr);

        if ($stmt->execute()) {
          echo "Insert was successfull!";
        } 
        else {
          echo "Error: " . $stmt . "<br>" . $conn->error;
        }

        $conn->close();
    }
    
      public static function insert_vehicleType()
    {
        $con = self::open();

        $sql = "INSERT INTO vehicleType (`type`, vehicleSize)
        VALUES ('car', 20);";
        $sql .= "INSERT INTO vehicleType (`type`, vehicleSize)
        VALUES ('mc', 10);";
        $sql .= "INSERT INTO vehicleType (`type`, vehicleSize)
        VALUES ('bike', 5);";

        if ($con->multi_query($sql) === TRUE) 
            {
            echo "New record created successfully";
            }
        else 
            {
            echo "Error: " . $sql . "<br>" . $con->error;
            }

        $con->close();

    }
    
    
      public static function Insert_Log($parkingmomentid, $cost, $timeexit)
      {
          $conn = self::open();
  
          $stmt = $conn->prepare("INSERT INTO `Log`(ParkingmomentID, Cost, TimeExit) VALUES(?,?,?);");
          $stmt->bind_param("iis", $parkingmomentid, $cost, $timeexit);
  
          if ($stmt->execute()) {
            echo "Insert was successfull!";
          } 
          else {
            echo "Error: " . $stmt . "<br>" . $conn->error;
          }
  
          $conn->close();
      } 
    
          //visa parkerade fordon
          public function ShowParkedVehicles()
      {
        $conn = self::open();

        $sql = <<<'SQL'
        SELECT v.RegNr, vt.Type, pm.TimeArrivle
        FROM parkingmoments AS pm
        INNER JOIN vehicle AS v ON pm.VehicleID = v.VehicleID
        INNER JOIN vehicletype AS vt ON v.VehicleTypeID = vt.VehicleType;
        SQL;


        $result = mysqli_query($conn, $sql);

        if($result->num_rows > 0)
        {
            while($row = $result->fetch_assoc()) {
                echo $row['RegNr'] . " | " . $row['Type'] . " | " . $row['TimeArrivle'] . "<br>";
              }
        }
        else{
            echo "No parked vehicles found!";
        }
        
      }
    
    //search for regnr db
     public function dbSearchRegNr($regnr)
      {
          $regnr = strtoupper($regnr);
          $conn = self::open();

          
          $sql = <<<'SQL'
          SELECT v.RegNr, pm.ParkingSpotID
          FROM parkingmoments AS pm
          INNER JOIN vehicle AS v ON pm.VehicleID = v.VehicleID;
          SQL;

          $result = $conn->query($sql);

          if ($result->num_rows > 0) 
          {
            while($row = $result->fetch_assoc()) 
            {
               if($row["RegNr"] == $regnr)
               {
                   return $row["ParkingSpotID"];
               }
            }
          } 
          $conn->close();
          return -1;
      }
    
}
?>
