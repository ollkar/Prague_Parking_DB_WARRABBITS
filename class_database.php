<?php

class Database
{
    const SERVERNAME = "localhost:3316";
    const USERNAME = "root";
    const PASSWORD = "";
    const DBNAME = "pragueparkingdb";

    public static function open() // Open connection to DB-server.
    {
        $connection = new mysqli(self::SERVERNAME, self::USERNAME,self::PASSWORD,self::DBNAME);

        if ($connection->connect_error) 
        {
            die("Connection failed: " . $connection->connect_error);
        }
        return $connection;
    }

    
}
?>