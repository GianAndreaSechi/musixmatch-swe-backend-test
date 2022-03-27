<?php
/**
* This class represent the database connection object
* METHODS:
*   - OpenConnection: handle the opening mySql connection to the database;
*/
class DBConnection{
    // private properties 'cause it won't be used outside the class
    private $dbhost = "localhost";
    private $dbuser = "root";
    private $dbpass = "";
    private $db = "musixmatch";

    public $connection;

    function OpenConnection()
     {
        $this->connection = null;

        try{
            $this -> connection = new mysqli($this->dbhost, $this->dbuser, $this->dbpass,$this->db) or die("Connect failed: %s\n". $this -> connection -> error);
        
            // Check connection
            if (!$this -> connection) {
                die("Connection failed: " . mysqli_connect_error());
            }
    
            return $this -> connection;
        } catch (Exception $exception) {
            echo "Error: " . $exception->getMessage();
        }
     }
     function firstConfiguration(){
         //check if database is setted
         //drop it
         //import sql file
         //create dummy data (puoi metterli direttamente nell'sql file)
     }
}
?>