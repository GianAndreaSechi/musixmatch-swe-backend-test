<?php
/**
* This class create a graph object user to explore every path possible between the adjancy table of databse (adjacent_stations);
* The result are put into a database table (all_possible_path)
* METHODS:
*   - addVertex: create a vertex into the graph;
*   - addEdge:  create an edge between two vertex (if the root vertex isn't exists it will be create);
*   - printGraph: it is only a debug function to print vertex and his edge;
*   - dfs: performing a Depth First Search to explore every path between two node;
*   - find_all_paths: just initialize the situation and call the dfs method;
*   - buildGraph: provide to load from database the adjacent station and create the graph structure;
*   - buildAllPath: provide to exlpore every single possible combination of start and exit point and find all possible paths between and put into databse;
*graph structure usgin associative array (it works like a dictionary in python)
*$graph = array(
*    'A' => array(
*        'B' => array(
*            'km' => 10,
*            'cost' => 11
*        ),
*        'F'=> array(
*            'km' => 10,
*            'cost' => 11
*        ),
*    ),
*    'B' => array(
*        'A' => array(
*            'km' => 10,
*            'cost' => 11
*        ), 
*        'D' => array(
*            'km' => 10,
*            'cost' => 11
*        ), 
*        'E'=> array(
*            'km' => 10,
*            'cost' => 11
*        )
*    )
*  );
*/
class Graph
{
    // Connection instance
    private $connection;

    // table name
    private $table_name = "adiacent_stations";

    //it is a directed weighted graph properties
  protected $graph;
  protected $visited = array();
  protected $all_path = array();
  protected $total_km_path = 0; //weight based on KMs
  protected $total_costs_path = 0; //weight based on Cost

  public function __construct($connection, $graph=array()) {
      $this->connection = $connection;
    $this->graph = $graph;
  }

  public function addVertex($vertex){
      /**
      * This method allow to create a vertex into the graph;
      */
    $this->graph[$vertex] = $vertex;
  }

  public function addEdge($from, $to, $weight, $cost){
      /**
      * This method allow to create an edge between two vertex (if the root vertex isn't exists it will be create);
      */
      if (array_key_exists($from,  $this->graph)){
          if (array_key_exists($to,  $this->graph[$from]) == FALSE){
            $this->graph[$from][$to] = array('km' => $weight, 'cost' => $cost);
          } 
      } else {
        $this->graph[$from] = array($to => array('km' => $weight, 'cost' => $cost));
      }
  }

  public function print_graph(){
      /**
      * DEBUG function to print vertex and his edge;
      */
    foreach($this->graph as $vertex => $edges){
        echo $vertex."->";
        foreach($edges as $edge => $properties){
            echo $edge."(".$properties['km'].",".$properties['cost'].")";
            if ($edge !== array_key_last($edges)) {
                echo "->";
            }
        };
        echo "<br>";
    }
  }

  public function dfs($from, $to, $current_path){
    /**
    * performing a Depth First Search algorithm to explore every path between two node;
    */
    array_push($this->visited, $from);
    if ($from === $to){
        $percorso = implode("-", $current_path);
        
        array_push($this->all_path, array($percorso => array('path'=>$current_path,'km' => $this->total_km_path, 'cost' => $this->total_costs_path)));
    } else {
        foreach($this->graph[$from] as $node => $props){
            if(in_array($node,$this->visited)==FALSE){
                //used to calculate the total, used as weighted tuple of edges.
                $this->total_costs_path += $props['cost'];
                $this->total_km_path += $props['km'];
                
                //insert into current_path the actual node
                array_push($current_path, $node);
                
                //recoursive calling DFS
                $this->dfs($node, $to, $current_path);

                //deleting last element in current_path
                array_pop($current_path);
                //as deleting the last element i have to sub the last value to total
                $this->total_costs_path -= $props['cost'];
                $this->total_km_path -= $props['km'];
            }
        }
    }
    array_pop($this->visited);
    }
    
    public function find_all_path($start, $end){
        /**
        * Depth first search starting from start-end destination;it initialize the situation and call the dfs method
        */
        $current_path = array();
        //setting to zero to avoid previous calculation
        $this->total_costs_path = 0;
        $this->total_km_path = 0;
        
        array_push($current_path, $start);
        
        $this->dfs($start, $end, $current_path);

        return $this -> all_path;
    }
    function buildGraph(){
        /**
        * Allow to load, from database, the adjacent station and create the graph structure that will used to build all possible path
        */
        $sql = "SELECT from_station_id, to_station_id, segment_km, segment_cost FROM adiacent_stations";
        $stmt = $this->connection->prepare($sql);
        if($stmt->execute()){
            $result = $stmt->get_result();
                if ($result->num_rows > 0) {// output data of each row
                    while($row = $result->fetch_assoc()) {
                        //create an edge of a graph with two adjacent station plus their weight in km and cost
                        $this -> addEdge($row["from_station_id"],$row["to_station_id"],$row["segment_km"], $row["segment_cost"]);
                       }
                   } else {
                       return array();
                    }
     }
     return $this;
    }
    function buildAllPaths(){
        /**
        * provide to exlpore every single possible combination of start and exit point and find all possible paths between 
        * and put into databse.
        */
            $sql = "SELECT station_id FROM motorway_stations"; //get all stations
            $stmt_from = $this->connection->prepare($sql);
            if($stmt_from->execute()){
                $result_from = $stmt_from->get_result();
                    if ($result_from->num_rows > 0) {// output data of each row
                        while($row_from = $result_from->fetch_assoc()) {
                            //for all station i'll check all possible combination with other stations except itself
                            $sql = "SELECT station_id FROM motorway_stations where station_id <> ?";
                            $stmt_to = $this->connection->prepare($sql);
                            $stmt_to->bind_param("i", $row_from['station_id']);
                            if($stmt_to->execute()){
                                $result_to = $stmt_to->get_result();
                                    if ($result_to->num_rows > 0) {// output data of each row
                                        while($row_to = $result_to->fetch_assoc()) {
                                            //find all path between from-to stations collected
                                            $path = $this -> find_all_path(intval($row_from["station_id"]),intval($row_to["station_id"]));
                                            foreach ($path as $p => $props){
                                                foreach($props as $key => $value){
                                                    //create the row in database with all possible path get from the graph
                                                    $stmt = $this->connection->prepare("
                                                    INSERT INTO all_possible_paths(`from_station_id`, `to_station_id`, `complete_path`, `total_km`, `total_cost`)
                                                    VALUES(?,?,?,?,?)");
                                                
                                                
                                                    $stmt->bind_param("iisdd", $row_from["station_id"], $row_to["station_id"], $key, $props[$key]['km'], $props[$key]['cost']);
                                                
                                                    $stmt->execute();
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    echo $this->connection->error;
                                }
                            
                        }
                    } else {
                        echo $this->connection->error;
                        }
            }
        }
}

?>