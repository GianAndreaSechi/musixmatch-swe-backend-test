<?php
/**
* This class represent the TravelLogs object;
* it provide to insert ENTER/EXIT on the Motorway method
* METHODS:
*   - createEnter: to insert an ENTER log;
*   - createExit:  to insert an EXIT log;
*   - read: possibility to read all travel logs from the database, or a specific travel log of single user;
*   - update: possibility to update a specific travel logs in the database;
*/
class TravelLogs{
    // Connection instance
    private $connection;

    // table name
    private $table_name = "vehicles_travel_logs";

    // public properties
    public $log_id;
    public $enter_station_id;
    public $exit_station_id;
    public $vehicles_license_plate;
    public $travel_date;

    public function __construct($connection, $log_id=NULL,$enter_station_id=NULL, $exit_station_id=NULL, $vehicles_license_plate=NULL, $travel_date=NULL) {
        $this->connection = $connection;
        
        $this->log_id = $log_id;
        $this->enter_station_id = $enter_station_id;
        $this->exit_station_id = $exit_station_id;
        $this->vehicles_license_plate = $vehicles_license_plate;
        $this->travel_date = $travel_date;
      }

    function createEnter(): bool{
        /**
        * This method insert into the database a single row of Travel Log by entering station and licence plate;
        * It insert into the database $this object so you have to create the correct one before launch the method;
        */
        $stmt = $this->connection->prepare("
            INSERT INTO ".$this->table_name."(`enter_station_id`, `exit_station_id`, `vehicles_license_plate`, `travel_date`)
            VALUES(?,?,?,?)");
        
        $this->enter_station_id = htmlspecialchars(strip_tags($this->enter_station_id));
        $this->exit_station_id = NULL;
        $this->vehicles_license_plate = htmlspecialchars(strip_tags($this->vehicles_license_plate));
        $this->travel_date = date('Y-m-d'); //inserting today date
        
        
        $stmt->bind_param("iiss", $this->enter_station_id, $this->exit_station_id, $this->vehicles_license_plate, $this->travel_date);
        
        if($stmt->execute()){
            return true;
        }
    
        return false;		 
    }
    function createExit(): bool{
        /**
        * This method update into the database the last row of Travel Log by entering station and licence plate;
        * This because it supposed to have a correct send by the "telepass like" device that not allow a discrepancy between data;
        */        
        if($this->exit_station_id != NULL){
            $stmt = $this->connection->prepare("
                SELECT `log_id`, `enter_station_id`, `exit_station_id`, `vehicles_license_plate`, `travel_date` FROM ".$this->table_name." 
                WHERE `vehicles_license_plate`= ? ORDER BY `travel_date` DESC, log_id DESC LIMIT 1");
            
            $stmt->bind_param("s", $this->vehicles_license_plate);
            
            if($stmt->execute()){
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    // output data of each row
                    $row = $result->fetch_assoc();
                    
                    $stmt = $this->connection->prepare("
                    UPDATE ".$this->table_name." set exit_station_id = ?
                    WHERE log_id = ?");
                
                    $stmt->bind_param("si", $this->exit_station_id,$row['log_id']);
                    $stmt->execute();
                }
                return true;
            }
        }
    
        return false;		 
    }
    
    function read($id=NULL){
        /**
        * This method get all TravelLog from the database and convert into json;
        * It get all data from the database or a specific one for specific customer passing the $id;
        */	
        if($id) {
            $stmt = $this->connection->prepare("
            SELECT `log_id`, `enter_station_id`, `exit_station_id`, `vehicles_license_plate`, `travel_date` FROM vw_".$this->table_name." 
            WHERE `user_id`= ? ORDER BY `travel_date` DESC");
            $stmt->bind_param("i", $id);					
        } else {
            $stmt = $this->connection->prepare("
            SELECT `log_id`, `enter_station_id`, `exit_station_id`, `vehicles_license_plate`, `travel_date` FROM vw_".$this->table_name." 
            ORDER BY `travel_date` DESC");		
        }		
        $stmt->execute();			
        $result = $stmt->get_result();		
        return json_encode(array("results" => $result->fetch_all()));	
    }

    function update(){
        /**
        * This method update into the database a single row of Travel Log;
        * It update into the database $this object so you have to create the correct one before launch the method;
        */ 
        $stmt = $this->connection->prepare("
            UPDATE ".$this->table_name." set enter_station_id= ?, exit_station_id = ?, vehicles_license_plate = ?, travel_date = ?
            WHERE log_id = ?");
    
        $this->enter_station_id = htmlspecialchars(strip_tags($this->enter_station_id));
        $this->exit_station_id = htmlspecialchars(strip_tags($this->exit_station_id));
        $this->vehicles_license_plate = htmlspecialchars(strip_tags($this->vehicles_license_plate));
        $this->travel_date = htmlspecialchars(strip_tags($this->travel_date));
    
        $stmt->bind_param("iiiss", $this->name, $this->description, $this->price, $this->category_id, $this->created, $this->id);
        
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }
}
?>