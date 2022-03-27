<?php
include_once 'config/bootstrap.php';


$db = new DBConnection();
echo "(1/6)open connection: ";
$conn = $db -> OpenConnection();
echo "Test passed<br>";


//*build the graph
echo "(2/6) Build Graph: ";
$g = new Graph($conn);
$g = $g -> buildGraph();
echo "Test passed<br> Printed graph:";
$g -> print_graph();


//used to put all possible path into database
echo "(3/6) Inserting all path in database: ";
$g -> buildAllPaths();
echo "Test passed<br>";

echo "(4/6) Setting new TravelLogs object: ";
$TravelLogs = new TravelLogs($conn,NULL,1,2,"AA001BB");
echo "Test passed <br>";

//travel logs enter/exit
echo "(5/6) Create an ENTER log: ";
if ($TravelLogs->CreateEnter()){
    echo "Enter Test Passed <br>";
} else {
    echo "Enter Test not Passed <br>";
}

echo "(6/6) Create an EXIT log: ";
if($TravelLogs->CreateExit()){
    echo "Test Passed<br>";
} else {
    echo "Test not Passed<br>";
}





// testing the function that is resposible to search all path between two stations.
//$path = $g -> find_all_path(1,6);
//print_r($path);

?>