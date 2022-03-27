<?php
/**
* This class represent the controller for TravelLogs and handle the action of the user (it could be ENTER/EXIT from the Motorway)
* METHODS:
*   - enterAction: handle the insert request and call an ENTER log action;
*   - exitAction:  handle the insert request and call an EXIT log action;
*/
class TravelController extends BaseController
{
    /**
     * "/travel/enter" Endpoint - Insert insert new enter in motorway
     * it insert into database a row with the entering station;
     */
    public function enterAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();

        if (strtoupper($requestMethod) == 'GET') {
            try {
                if(isset($_GET['enter_station_id']) && isset($_GET['vehicles_license_plate'])){
                    $db = new DBConnection();
                    $conn = $db -> OpenConnection();
                    $TravelLogs = new TravelLogs($conn,NULL,$_GET['enter_station_id'],NULL,$_GET['vehicles_license_plate']);
                    if ($TravelLogs->createEnter()){
                        $responseData = json_encode(array("success" => "Inserted an ENTER for ".$_GET['enter_station_id']." station for ".$_GET['vehicles_license_plate']));                                
                    }else{
                        $strErrorDesc = 'Error occured during inserting an ENTER!';
                    };
                    $db -> connection -> close();
                } else {
                    $strErrorDesc = 'Wrong parameter!';
                }
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
 
        // send output
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }
    /**
     * "/travel/exit" Endpoint - Insert insert new exit in motorway for a user
     * it will update the last row of a user;
     */
    public function exitAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
 
        if (strtoupper($requestMethod) == 'GET') {
            try {
                if(isset($_GET['exit_station_id']) && isset($_GET['vehicles_license_plate'])){
                    $db = new DBConnection();
                    $conn = $db -> OpenConnection();
                    $TravelLogs = new TravelLogs($conn,NULL,NULL,$_GET['exit_station_id'],$_GET['vehicles_license_plate']);
                    if ($TravelLogs->createExit()){
                        $responseData = json_encode(array("success" => "Inserted an EXIT for ".$_GET['exit_station_id']." station for ".$_GET['vehicles_license_plate']));                                
                    }else{
                        $strErrorDesc = 'Error occured during inserting an EXIT!';
                    };
                } else {
                    $strErrorDesc = 'Wrong parameter!';
                }
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
 
        // send output
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }
}
?>