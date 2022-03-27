<?php
/**
* This class represent the controller for Customer API and handle the action of the backoffice user
* METHODS:
*   - amountDueAction: give the data about the amount due of all customers in a specific month; 
*                      if customer_id is passed it will be give the amount due of this customer in a specific month;
*/
class CustomerController extends BaseController
{
    /**
     * "/customer/amountDue" Endpoint - Get mount of due for single customer or each customer by month.
     */
    public function amountDueAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
 
        if (strtoupper($requestMethod) == 'GET') {
            try {
                if(isset($_GET['customer_id'])){
                    $customer_id = $_GET['customer_id'];
                }
                if(isset($_GET['month'])){
                    $month = $_GET['month'];
                }
                
                if (isset($month)){
                    $db = new DBConnection();
                    $conn = $db -> OpenConnection();
                    
                    $sql = "SELECT user_id, username, month(travel_date) as month, round(SUM(total_cost),2) as total_cost 
                    FROM vw_vehicles_paths_cost
                    WHERE month(travel_date)=? ";
                    if(isset($customer_id)){
                      $sql =  $sql."and user_id=?"; 
                    }
                    $sql=$sql." GROUP BY user_id, username, month(travel_date)";
            
                    $stmt = $conn->prepare($sql);
                    if(isset($month) and isset($customer_id)){
                        $stmt->bind_param("ii", $month, $customer_id);
                    } else {
                        $stmt->bind_param("i", $month);
                    }
                    
                    if($stmt->execute()){
            
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {// output data of each row
                            $rows=array();
                            while($row = $result->fetch_assoc()) {
                                array_push($rows, $row);
                            }
                            $responseData = json_encode($rows);
                        }
                    } else {
                        $responseData=array("error" => "error during execution");
                    }
                    $db -> connection -> close();
            
                } else {
                    $responseData=array("error" => "month not selected");
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