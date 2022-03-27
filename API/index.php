<?php
/**
* Main API page that handle the url request receiving
* Module->Method allowed:
*   - /travel/
*       - enter
*       - exit
*   - /customer/
*       - amountDue: withoud customer_id give you all customers amount due, with customer_id give you only the specific data about customer_id
* Method allowed:
*   - enter
*   - exit
* Url like: https://localhost/musixmatch/api/index.php/{MODULE_NAME}/{METHOD_NAME}?paramete1={PARAM1_VALUE}&parameter2={PARAM2_VALUE}....;
*/

include_once 'config/bootstrap.php';

//get the url passed and explod it
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

//check if called a allowed method (case sensitive)
if(isset($uri[4]) && ($uri[4] != 'travel' && $uri[4] != 'customer') || !isset($uri[5])){
    header("HTTP/1.1 404 Not Found");
    exit();
} 

//using a user_controller that allow to choose the correct method
require PROJECT_ROOT_PATH."/controller/".$uri[4]."_controller.php";
$strControllerName = $uri[4]."Controller";

$objFeedController = new $strControllerName();

$strMethodName = $uri[5] . 'Action';

$objFeedController->{$strMethodName}();

?>