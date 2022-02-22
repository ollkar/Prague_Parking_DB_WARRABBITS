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
            //insert into vehicle Moments
    public static function insert_vehicleMoments($parkingSpot, $VehicleID, $TimeArrival)
    {
        $conn = self::open();
  
        $stmt = $conn->prepare("INSERT INTO parkingmoments(parkingSpotID, VehicleID, TimeArrival) VALUES(?,?,?);");
        $stmt->bind_param("iis", $parkingSpot, $VehicleID, $TimeArrival);

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
        SELECT v.RegNr, vt.Type, pm.TimeArrival
        FROM parkingmoments AS pm
        INNER JOIN vehicle AS v ON pm.VehicleID = v.VehicleID
        INNER JOIN vehicletype AS vt ON v.VehicleTypeID = vt.VehicleType;
        SQL;


        $result = mysqli_query($conn, $sql);

        if($result->num_rows > 0)
        {
            while($row = $result->fetch_assoc()) {
                echo $row['RegNr'] . " | " . $row['Type'] . " | " . $row['TimeArrival'] . "<br>";
              }
        }
        else{
            echo "No parked vehicles found!";
        }
              $conn->close();
        
      }
    
    
    //----------------FEL FEL FEL FEL FEL------------------------------------------DENNA METODEN OCH NÄSTA :(
    public function Get_parkingMomentID($regNr)
    {
      $conn = self::open();
      $spotID = self::dbSearchRegNr($regNr);
      if($spotID == -1){
        $conn->close();
        throw new mysqli_sql_exception("fordonet har inte parkerats");
      }

      $sql = <<<'SQL'
      SELECT pm.parkingmomentsID
      FROM vehicle AS v
      INNER JOIN parkingMoments AS pm ON v.VehicleID = pm.VehicleID
      INNER JOIN ParkingSpot as ps ON pm.ParkingSpotID = ps.ParkingSpotID
      WHERE  regNr =   
      SQL;

      $sql.= '"'.$regNr.'"'.";";
      $result = mysqli_query($conn, $sql);

      if ($result->num_rows > 0) 
          {
            while($row = $result->fetch_assoc()) 
            {

              $momentID = $row["parkingmomentsID"];
              return $momentID;
            }
          } 
          else
          {
            throw new mysqli_sql_exception("0 rows returned");
          }
      
      $conn->close();
      //"spot" och "vehicle" ska skickas vidare
    }


    public function DeleteParkedVehicle($regNr)
    {
      $momentID = self::Get_parkingMomentID($regNr);
      $conn = self::open();

      $stmt = $conn->prepare("INSERT TO Log WHERE parkingmomentsID = ?");
      $stmt->bind_param("i", $momentID);

      $stmt2 = $conn->prepare("DELETE FROM ParkingMoments WHERE parkingmomentsID = ?");
      $stmt2->bind_param("i", $momentID);
  
      $conn->close();

    }
//----------------FEL FEL FEL FEL FEL------------------- DOM TVÅ ÅVANSTÅENDE METODERNA SUUUUUGER
    
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
    
       //find empty spot
       public function findemptyspot($vehicle)
      {
        $conn = self::open();
        $sql = <<<'SQL'
            SELECT ps.ParkingSpotID, ps.SpotSize
            FROM parkingspot AS ps
            LEFT JOIN parkingmoments AS pm ON pm.ParkingSpotID = ps.ParkingSpotID
            LEFT JOIN vehicle AS v On v.VehicleID = pm.VehicleID
            LEFT JOIN vehicletype AS vt ON vt.VehicleType = v.VehicleTypeID;
        SQL;

        $result = $conn->query($sql);
         
        while($row = $result->fetch_assoc()) 
        {
            $space_left = $row["SpotSize"] - $vehicle->get_vehicleSize();
            if($space_left >= 0)
              {
                  return $row["ParkingSpotID"];
              }
        }
        $conn->close();
        return -1;   
    }
    
     public static function Get_SpotSize($spot)
    {
        $conn = self::open();

        $sql = "SELECT spotsize FROM parkingspot WHERE parkingspotID = $spot;";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) 
        {
        // output data of each row
        while($row = $result->fetch_assoc()) 
        {
            return $row["spotsize"];
        }
        } 
        else 
        {
            echo "0 results";
        }
        $conn->close();
    }

    public static function Update_SpotSize_Sub($spot, $vehicle)
    {
        $conn = self::open();

        $newsize = (self::Get_SpotSize($spot)) - ($vehicle->get_vehicleSize());
        
        $sql = "UPDATE parkingspot
        SET spotsize = $newsize
        WHERE parkingspotID=$spot;";

        if ($conn->query($sql) === TRUE) 
        {
            echo "Updated successfully";
        }
        else 
        {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();

    }
    
    public static function Update_SpotSize_Add($spot, $vehicle)
    {
        $conn = self::open();

        $newsize = (self::Get_SpotSize($spot)) + ($vehicle->get_vehicleSize());
        
        $sql = "UPDATE parkingspot
        SET spotsize = $newsize
        WHERE parkingspotID=$spot;";

        if ($conn->query($sql) === TRUE) 
        {
            echo "Updated successfully";
        }
        else 
        {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    }

    public static function Reset_Database() // SCRIPT SQLFILE TO DATABASE
    {
        // open connection.
        $conn = self::open(); 

        // Create database with tables.
        $dbscript = file_get_contents("prague_parking2.sql");

        if ($conn->multi_query($dbscript) === TRUE) 
        {
            $conn->close();
            return "PragueParking was created successfully!"; // return $_SESSION['message'] ? to display message freely
        } 
        else 
        {
            $conn->close();
            throw new mysqli_sql_exception("Error creating table: " . $conn->error);
        }
     
    }
    
    //move vehicle inte klar!!!!!!!!!!!!!
    public function moveVehicle($vehicle, $ParkingSpotID)
    {
        $conn = self::open();
        $regnr = $vehicle->get_regnr();

        $sqlfindlastspot =
        "SELECT ParkingSpotID
        FROM parkingmoments AS pm
        INNER JOIN vehicle AS v 
        ON v.VehicleID = pm.VehicleID
        WHERE v.RegNr = '$regnr';";

        $sqlupdate = 
        "UPDATE parkingmoments AS pm
        INNER JOIN vehicle AS v 
        ON v.VehicleID = pm.VehicleID
        SET pm.ParkingSpotID = $ParkingSpotID
        WHERE v.RegNr = '$regnr';";

        

        $spaceleft = self::Get_SpotSize($ParkingSpotID) - $vehicle->get_vehicleSize();
        if($spaceleft >= 0)
        {
            $lastspot = (int)$conn->query($sqlfindlastspot); //sparar tidigare plats
            self::Update_SpotSize_Sub($ParkingSpotID, $vehicle); //ändra storlek på ny plats
            self::Update_SpotSize_Add($lastspot, $vehicle); //uppdatera tidigare plats
            $conn->query($sqlupdate); //flyttar fordon till ny plats
           

            return $vehicle->get_regnr() . " moved to parkingspot: " . $ParkingSpotID;
        }
        else 
        {
          return "No space left on parkingspot: " . $ParkingSpotID;
        }

        $conn->close();
        
    }
    
}
?>
