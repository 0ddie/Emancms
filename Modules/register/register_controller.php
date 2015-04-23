<?php

function register_controller() {
    global $route;

    if ($route->format == 'json') {

        if ($route->action == 'create') {

            
            /*
             * Decode the Json string to find the values for Apikey, Nodeip and the timeout from the starter packet
             */
            if (isset($_GET["apikey"])) {
                $apikey = $_GET ["apikey"];
            }
            if (isset($_GET["nodeip"])) {
                $nodeip = $_GET ["nodeip"];
            }
            if (isset($_GET["timeout"])) {
                $timeout = $_GET ["timeout"];
            }
            
            if (strlen ($apikey)!= 32){
                misformedError();
                return array('content'=> " Json string wrong length");
            }
            if (!ip2long ($nodeip)){
                misformedError();
                return array('content'=> " Node ip incorrect");
            }
            /*
             * Check if the node that is trying to register has been registered previously, if not add to the table Node_reg
             */
            if (exists($nodeip) === 0) {
                addNode($nodeip);
            } else {
                print_r("Already Registered Node");
            }
                
        }else{misformedError($route);}
    }else{misformedError($route);}
}
/*
 * Function to see if node is already in table Node_Reg
 */
function exists($nodeip) {
    global $mysqli;
    $result = $mysqli->query("SELECT FromAddress FROM Node_reg WHERE `FromAddress` = '$nodeip'");
    if ($result->num_rows === 1) {
        nodeMessage($nodeip);
        return 1;
    } else {
        return 0;
    }
}
/*
 * Function to add node to table Node_reg
 */
function addNode($nodeip) {
    global $mysqli;
    $mysqli->query("INSERT INTO Node_reg (FromAddress) VALUES ('$nodeip')");
    nodeMessage($nodeip);
    print_r ("Node added to Node_reg");
}
/*
 * Function to pull node id from table Node_reg
 */
function nodeMessage($nodeip) {
    global $mysqli;
    $result = $mysqli->query("SELECT NodeID FROM `Node_reg` WHERE `FromAddress` = '$nodeip' ");
    $row = mysqli_fetch_assoc($result);
    print_r($row);
}

function misformedError(){
    print_r("Json string misformed");
}
