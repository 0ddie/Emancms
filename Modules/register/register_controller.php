<?php

function register_controller() {
      
    global $route, $mysqli, $redis, $feed, $session, $user;
    require "Modules/register/register_model.php";
    $register = new register($mysqli);
    
    require "Modules/input/input_model.php";
    $input = new Input($mysqli,$redis, $feed);
    $ender = 0;
    
    require "Modules/input/process_model.php"; // 886
    $process = new Process($mysqli,$input,$feed);

    $process->set_timezone_offset($user->get_timezone($session['userid']));
    
    
    if ($route->format == 'json') {
        //$fTime = time();
        if ($route->action == 'test'){
           /* 
            $userid = $session['userid'];
            
            $groupIDDesc = $this->groupIDDescGetter($attributeUid);
            $attributeIDDesc = $this->attributeIDDescGetter($attributeUid);
            $register->feedCreator($groupIDDesc,$attributeIDDesc);
            $id=($register->feed_id_getter());
            print_r($id);
            $nodeid = 5;
            $tag = ("N".$nodeid);
            $register->set_feed_fields($id, $tag);
            //$input->add_process($process,$session['userid'], get('inputid'), get('processid'), get('arg'), get('newfeedname'), get('newfeedinterval'),get('engine'));
             $arg = -1;
             $inputid = $register->inputIdGetter($reformattedJson);
             $processid = 1;
             //$input->add_process($process,$userid,$inputid,$processid,$arg); 
             
            //$userid = $session['userid'];
            //$register->getAttributesByNode($userid);
            * *
            */
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
            if (isset($_GET["fromAddress"])){
                $nodeIP = $_GET ["fromAddress"];
            }
            if (isset($_GET["timeout"])) {
                $timeout = $_GET ["timeout"];
            }
            
            
           // $timeDiff = timeoutChecker($timeStart);
            
            if ($register->apikeycheck($apikey) === 1) {

                return array('content' => "Apikey wrong length");
            } else if ($register->apikeycheck($apikey) === 2) {
                return array('content' => "Apikey incorrect");
            }
            
            if ($register->checkNodeIP($nodeIP)===1){
            return array('content' => " incorrectly formatted IP Address");
            }

            if ($register->checkMACAddress($nodeMAC)===1){
                return array('content' => " incorrectly formatted MAC Address");
            }
            /*
             * What are the nodeid and Name for the create input
             */
                

            if ($register->exists($nodeMAC) === 0) {
                //$register->nodeIDIncrementer();
                $register->addNode($nodeMAC, $nodeIP);
            } else {
                print_r(" Already Registered Node");
            }
            //returns the nodeid
            //start timer 1
            //}while($timeout>$timeDiff); 
            $startTime = $register->startTimer();
            $register->timedOut($startTime, $ender, $timeout);
        } elseif ($route->action == 'setup') {
            //stop timer 1
            //sleep(16);
            $ender = 1;

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

                return array('content' => "Apikey wrong length");
            } else if ($register->apikeycheck($apikey) === 2) {
                return array('content' => "Apikey incorrect");
            }

            if ($register->correctNodeID($nodeid) == 1) {
                return array('content' => "Node ID Mismatch");
            }

            /*if ($register->jsonStringError($json, $nodeid) === 1) {
           a     $register->misformedError();
                return array('content' => "Json section wrong length");
            }
             * 
             */
            $nodeidL = strlen($nodeid);
            
            

            /*if ($register->nodeIDConstant($nodeid, $json, $nodeidL) === 1) {

                $register->misformedError();
                return array('content' => "Node id's are different within String");
            }
             * 
             */
            
            if($register->correctInputJson($json,$nodeid)===1){
            return array('content' => "Already been inputted with this node ID");
            }
            $doing = 0;
            $reformattedJson = $register->jsonParse($json,$nodeid , $doing);
            $space = strpos($reformattedJson, '-', 0);
            $attributeUid = substr($reformattedJson, $space);
            
            $groupIDDesc = $register->groupIDDescGetter($attributeUid);
            $attributeIDDesc = $register->attributeIDDescGetter($attributeUid);
            $id=($register->feed_id_getter());
            $userid = $session['userid'];
            


            $name = ($groupIDDesc." ".$attributeIDDesc);
            $register->inputCreator($nodeid, $input, $reformattedJson);
            $tag = ("N".$nodeid);
            $inputid = $register->inputIdGetter($reformattedJson);
            $register->feedCreator($groupIDDesc, $attributeIDDesc, $attributeUid);
            $id = $register->feed_id_getter();
            $arg = $id;
            $processid = 1;
            $input->add_process($process,$userid,$inputid,$processid,$arg); 
            $register->set_feed_fields($id, $tag, $name);

        } else {
            //$register->misformedError($route);
        }
    } else {
        //$register->misformedError($route);
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

//Working Example of "Create" json string: http://localhost/OpenEMan/register/create.json?apikey=4903c8a630a99c63251b5a34ac043ba5&nodeMAC=01:23:45:67:89:ab&fromAddress=123.123.123.123&timeout=15
//Working Example of "setup" json string: http://localhost/OpenEMan/register/setup.json?apikey=7c399b2a696c8e1d3efebb7767fba593&node=50&json=55555566666677777&timeout=225