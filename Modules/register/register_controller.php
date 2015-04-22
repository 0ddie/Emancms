<?php

/**
 * @return array|string
 */
function register_controller() {
    global $route, $mysqli;
//            $this->mysqli = $mysqli;

    if ($route->format == 'json') {

        if ($route->action == 'create') {



            if (isset($_GET["apikey"])) {
                $apikey = $_GET ["apikey"];
//return array('content' => $apikey);
            }
            if (isset($_GET["nodeip"])) {
                $nodeip = $_GET ["nodeip"];
//return array('content' => $nodeip);
            }
            if (isset($_GET["timeout"])) {
                $timeout = $_GET ["timeout"];
//return array('content' => $timeout);
            }
//return array ('content' => $apikey . $nodeip . $timeout);
// print_r($apikey,$nodeip,$timeout);
// __construct($mysqli/*,$redis,$feed*/);
//$result = $mysqli->query("SELECT FromAddress FROM Node_reg WHERE `FromAddress` = '$nodeip'");
//if ($result->num_rows == 1) $yes = 1; else $yes = 0;
//return array ('content' => $yes);
//if ($yes = "yes"){
//$mysqli->query("INSERT INTO Node_reg (FromAddress) VALUES ('$nodeip')");
//}
            if (exists($nodeip) === 0) {
                addNode($nodeip);
                nodeMessage($nodeip);
            } else {
                print_r("Already Registered Node");
                nodeMessage($nodeip);
            }
           
        }
    }
}

function exists($nodeip) {
    global $mysqli;
    $result = $mysqli->query("SELECT FromAddress FROM Node_reg WHERE `FromAddress` = '$nodeip'");
    if ($result->num_rows === 1) {
        return 1;
    } else {
        return 0;
    }
}

function addNode($nodeip) {
    global $mysqli;
    $mysqli->query("INSERT INTO Node_reg (FromAddress) VALUES ('$nodeip')");
   // return array('content' => "Worked");
}
function nodeMessage($nodeip) {
    global $mysqli;
   // $nodeID = 0;
    $result = $mysqli->query("SELECT NodeID FROM `Node_reg` WHERE `FromAddress` = '$nodeip' ");
    return array('contain' => $result);
    
}
 
 