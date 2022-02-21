<?php

class Database
{
    const SERVERNAME = "localhost";
    const USERNAME = "root";
    const PASSWORD = "";
    const DBNAME = "";

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
