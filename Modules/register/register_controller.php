<?php

function register_controller() {
    global $route;
    require "Modules/register/register_model.php";
    $register = new register($mysqli);

    if ($route->format == 'json') {
        $timeStart = microtime(true);
        if ($route->action == 'test'){
            $json = '11111,22222,33333,44444';
            $register->jsonParse($json);
                
        
        }
        if ($route->action == 'create') {


            /*
             * Decode the Json string to find the values for Apikey, Nodeip and the timeout from the starter packet
             */
            if (isset($_GET["apikey"])) {
                $apikey = $_GET ["apikey"];
            }
            if (isset($_GET["nodeMAC"])) {
                $nodeMAC = $_GET ["nodeMAC"];
            }
            if (isset($_GET["timeout"])) {
                $timeout = $_GET ["timeout"];
            }
            
           // $timeDiff = timeoutChecker($timeStart);
            if ($register->apikeycheck($apikey) === 1) {

                return array('content' => "Apikey too long");
            } else if ($register->apikeycheck($apikey) === 2) {
                return array('content' => "Apikey incorrect");
            }


            /*if ($register->ipchecker($nodeMAC) === 1) {
                return array('content' => " Node mac incorrect");
            }*/
            if ($register->checkMACAddress($nodeMAC)===1){
                return array('content' => " incorrectly formatted MAC Address");
            }
            /*
             * What are the nodeid and Name for the create input
             */
                

            if ($register->exists($nodeMAC) === 0) {
                $register->nodeIDIncrementer();
                $register->addNode($nodeMAC);
            } else {
                print_r(" Already Registered Node");
            }
            //}while($timeout>$timeDiff); 
        } elseif ($route->action == 'setup') {

            if (isset($_GET["apikey"])) {
                $apikey = $_GET ["apikey"];
            }

            if (isset($_GET["node"])) {
                $nodeid = $_GET ["node"];
            }

            if (isset($_GET["json"])) {
                $json = $_GET ["json"];
            }

            if (isset($_GET["timeout"])) {
                $timeout = $_GET ["timeout"];
            }

            //$timeDiff2 =timeoutChecker($timeStart);
            //do{

            if ($register->apikeycheck($apikey) === 1) {

                return array('content' => "Apikey too long");
            } else if ($register->apikeycheck($apikey) === 2) {
                return array('content' => "Apikey incorrect");
            }

            if ($register->correctNodeID($nodeid) == 1) {
                return array('content' => "Node ID Mismatch");
            }

            /*if ($register->jsonStringError($json, $nodeid) === 1) {
                $register->misformedError();
                return array('content' => "Json section wrong length");
            }
             * 
             */
            $nodeidL = strlen($nodeid);
            

            if ($register->nodeIDConstant($nodeid, $json, $nodeidL) === 1) {

                $register->misformedError();
                return array('content' => "Node id's are different within String");
            }
            
            if ($register->jsonParse($json,$nodeid)===1){
                print_r("Yes");
            }

        } else {
            $register->misformedError($route);
        }
    } else {
        $register->misformedError($route);
    }
    
}

/*
function addNode($nodeip) {
    global $mysqli;
    $mysqli->query("INSERT INTO Node_reg (FromAddress) VALUES ('$nodeip')");
    $register->nodeMessage($nodeip);
    print_r("Node added to Node_reg");
}

function timeoutChecker($timeStart) {
    $timeCurr = microtime(true);
    $timeDiff = $timeCurr - $timeStart;
    return $timeDiff;
}

*/

//Working Example of "Create" json string: http://localhost/OpenEMan/register/create.json?apikey=c2fe22e1099b01c7bb380b530e0508c7&nodeip=xxx.xxx.xxx.xxx&timeout=15
//Working Example of "setup" json string: http://localhost/OpenEMan/register/setup.json?apikey=7c399b2a696c8e1d3efebb7767fba593&node=50&json=55555566666677777&timeout=225