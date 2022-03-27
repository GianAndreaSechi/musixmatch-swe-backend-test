<?php
//used to calculate the correct root path and used to make dynamic all path
define("PROJECT_ROOT_PATH", __DIR__ . "/../");

//including the mysql database configuration
require_once PROJECT_ROOT_PATH.'/class/db_connection.php';

//include model/class about the API
require_once PROJECT_ROOT_PATH.'/class/travel_logs.php';
require_once PROJECT_ROOT_PATH.'/class/graph.php';

//including the base controller
require_once PROJECT_ROOT_PATH.'/controller/base_controller.php';

?>