<?php

function register_controller() {

    global $route, $mysqli, $redis, $feed, $session, $user, $feed_settings, $log;
    require "Modules/register/register_model.php";
    $register = new register($mysqli);

    require "Modules/feed/feed_model.php";
    $feed = new Feed($mysqli, $redis, $feed_settings);

    require "Modules/input/input_model.php";
    $input = new Input($mysqli, $redis, $feed);
    $ender = 0;

    require "Modules/input/process_model.php"; // 886
    $process = new Process($mysqli, $input, $feed);

    include_once "Modules/log/EmonLogger.php";
    $log = new EmonLogger();
    
    /*
     * some form of apikey checker will have to be implemented here
     */
    $userid = $session['userid'];
    //$log->set_logfile(__DIR__ . '/register'.$userid.'.log');
    $log->set_logfile(__DIR__ . '/register.log');


    $verbose = TRUE;

    if (isset($registerLogMode) === false) {
        /* registerLogMode is defined in settings.php */
        $verbose = TRUE;
    } else {
        $verbose = $registerLogVerbose;
    }

//require "Modules/log/EmonLogger.php";
//$process->set_timezone_offset($user->get_timezmone($session['userid']));

    if ($register->tablesEmpty() === 1) {
                    $register->groupIdFiller();
                    $register->attributeIdFiller();
                    if ($verbose == TRUE) {
                        $log->info("group id and attribute id tables populated");
                    }
                } elseif ($verbose == TRUE) {
                    $log->info("group id and attribute id tables unchanged");
                }

    if ($route->format == 'json') {
//$fTime = time();
        if ($verbose == TRUE) {
            $log->info("json string recieved");
            if ($route->action == 'test') {
                // print_r($registerLogVerbose);
                $log->warn("Wales");
            }
            if ($route->action == 'create') {
                if ($verbose == TRUE) {
                    $log->info("creating node");
                }


                
                /*
                 * Decode the Json string to find the values for Apikey, Nodeip and the timeout from the starter packet
                 */
                if (isset($_GET["apikey"])) {
                    $apikey = $_GET ["apikey"];
                }
                if (isset($_GET["nodeMAC"])) {
                    $nodeMAC = $_GET ["nodeMAC"];
                }
                if (isset($_GET["fromAddress"])) {
                    $nodeIP = $_GET ["fromAddress"];
                }
                if (isset($_GET["timeout"])) {
                    $timeout = $_GET ["timeout"];
                }

                if ($verbose == TRUE) {
                    $log->info("Node MAC address: " . $nodeMAC . " Node IP address: " . $nodeIP . " Timeout: " . $timeout);
                }
                
                $userid = $session['userid'];
                
                
// $timeDiff = timeoutChecker($timeStart);

                if ($register->apikeycheck($apikey) === 1) {
                    $log->warn("Apikey wrong length");
                    return array('content' => "Apikey wrong length");
                } else if ($register->apikeycheck($apikey) === 2) {
                    $log->warn("Apikey wrong length");
                    return array('content' => "Apikey incorrect");
                }

                if ($register->checkNodeIP($nodeIP) === 1) {
                    $log->warn("incorrectly formatted IP Address");
                    return array('content' => " incorrectly formatted IP Address");
                }

                if ($register->checkMACAddress($nodeMAC) === 1) {
                    $log->warn("incorrectly formatted MAC Address");
                    return array('content' => " incorrectly formatted MAC Address");
                }
                /*
                 * What are the nodeid and Name for the create input
                 */


                if ($register->exists($nodeMAC,$userid) === 0) {
                    $nodeid = $register->addNode($nodeMAC, $nodeIP, $userid);
                
                if ($verbose == TRUE) {
                    //$nodeid = $register->nodeMessage($nodeMAC);
                    $log->info("Node ID assigned to this node: ".$nodeid);
                    return array('content' => $nodeid);
                }
                
                }else{
                    $nodeid = $register->nodeMessage($nodeMAC,$userid);
                    $log->warn("Already Registered Node, ID: " . $nodeid);
                    return array('content' => $nodeid);
                }
//                $log->info("The Node ID assigned to this node is: ".$nodeid);

//returns the nodeid
//start timer 1
//}while($timeout>$timeDiff); 
                $startTime = $register->startTimer();
                $register->timedOut($startTime, $ender, $timeout);
            } elseif ($route->action == 'setup') {

                if ($verbose == TRUE) {
                    $log->info("configuring node");
                }

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
                if ($verbose == TRUE) {
                    $log->info(" Node ID" . $nodeid . " Timeout: " . $timeout);
                }
//$timeDiff2 =timeoutChecker($timeStart);
//do{

                if ($register->apikeycheck($apikey) === 1) {
                    $log->warn("Apikey wrong length");
                    return array('content' => "Apikey wrong length");
                } else if ($register->apikeycheck($apikey) === 2) {
                    $log->warn("Apikey incorrect");
                    return array('content' => "Apikey incorrect");
                }

                if ($register->incorrectNodeID($nodeid) == 1) {
                    $log->warn("Node ID Mismatch");
                    return array('content' => "Node ID Mismatch");
                }

                /* if ($register->jsonStringError($json, $nodeid) === 1) {
                  return array('content' => "Json section wrong length");
                  }
                 * 
                 */
                $nodeidL = strlen($nodeid);



                /* if ($register->nodeIDConstant($nodeid, $json, $nodeidL) === 1) {

                  return array('content' => "Node id's are different within String");
                  }
                 * 
                 */

                if ($register->correctInputJson($json, $nodeid) === 1) {
                    $log->warn("Already been inputted with this node ID");
                    return array('content' => "Already been inputted with this node ID");
                }
                $doing = 0;
                $reformattedJson = $register->jsonParse($json, $nodeid, $doing);
                if (is_int($reformattedJson)) {
                    $log->warn("  ");
                    switch ($reformattedJson) {
                        case 1:
                            $log->warn("Incorrectly formatted group ID");
                            return array('content' => "Incorrectly formatted group ID");
                        case 2:
                            $log->warn("Group Id not found in specification");
                            return array('content' => "Group Id not found in specification");
                        case 3:
                            $log->warn("Incorrectly formatted attribute ID");
                            return array('content' => "Incorrectly formatted attribute ID");
                        case 4:
                            $log->warn("Attribute Id not found in specification");
                            return array('content' => "Attribute Id not found in specification");
                        case 5:
                            $log->warn("Not in Attribute Number range");
                            return array('content' => "Not in Attribute Number range");
                        case 6:
                            $log->warn("Not a correctly formatted Attribute Number");
                            return array('content' => "Not a correctly formatted Attribute Number");
                        case 7:
                            $log->warn("Already added to attributes table");
                            return array('content' => "Already added to attributes table");
                    }
//return array('content' => "  ");
                }
                $space = strpos($reformattedJson, '-', 0);
                $start = $space + 1;
                $attributeUid = substr($reformattedJson, $start);
                //$timeout = 15;

                $trimmer = substr($reformattedJson, $space);

                $reformattedJson = trim($reformattedJson, $trimmer);

                $groupIDDesc = $register->groupIDDescGetter($attributeUid);
                $attributeIDDesc = $register->attributeIDDescGetter($attributeUid);
//$id=($register->feed_id_getter());
                $userid = $session['userid'];




                $name = ("This is a " . $groupIDDesc . " measuring " . $attributeIDDesc);

                $register->inputCreator($nodeid, $input, $reformattedJson);


                $tag = ("N" . $nodeid);
                $inputid = $register->inputIdGetter($reformattedJson);
                $register->updateAttributesTableForInput($inputid, $attributeUid);

                $id = $register->feedCreator($groupIDDesc, $attributeIDDesc, $attributeUid);
//$id = $register->feed_id_getter();

                $arg = $id;
                $processid = 1;
                $input->add_process($process, $userid, $inputid, $processid, $arg);
                $register->set_feed_fields($id, $tag, $name);
                if ($verbose == TRUE) {
                    $log->info("Node with an ID of: " . $nodeid . " Which is a : " . $groupIDDesc . " Which is measuring : " . $attributeIDDesc);
                }
                $register->updateAttributesTableForFeed($id, $attributeUid);
                if ($verbose == TRUE) {
                    $log->info("Attribute added to attributes table. Attribute Uid: " . $attributeUid);
                }
            } elseif ($route->action == 'controller') {
                if ($verbose == TRUE) {
                    $log->info("Controller route taken");
                }

                if (isset($_GET["nodeid"])) {
                    $nodeid = $_GET ["nodeid"];
                }

                if (isset($_GET["attributeuid"])) {
                    $attributeUid = $_GET ["attributeuid"];
                }

                if (isset($_GET["value"])) {
                    $value = $_GET ["value"];
                }
                if (isset($_GET["timeout"])) {
                    $timeout = $_GET ["timeout"];
                }
                /* if ($verbose == TRUE) {
                  $log->info("ID of the node of the value that needs to be changed: ".$nodeid." ID of the attribute that needs to be changed ".$attributeUid."Value to be changed too".$value);
                  } */
                $apikey = "Blahhhhhhh";
                $nodeid = "76";
                $value = "50";
                $attributeUid = "136";
                $timeout = 15;

                $nodeIP = $register->getNodeIP($nodeid);
                $inputid = $register->getInputID($attributeUid);
                $name = $register->getName($inputid);
                $message = ($name . "-" . $value);
                //print_r($message);
                /* if ($register->http_response($nodeIP, '200', $timeout) == TRUE) { */// returns true if the response takes less than 3 seconds and the response code is 200 


                $result = $register->sendValueToNode($nodeIP, $message, $apikey, $nodeid, $timeout);
                //print_r($result);

                if ($result !== 0) {
                    $log->warn("HTTP Error code:" . $result);
                }

                /*
                  }else{
                  $httpcode=$register->http_response($nodeIP, '200', $timeout);
                  $log->warn("HTTP Error code:".$httpcode);
                  return array ('content'=>"HTTP Error code:".$httpcode);
                  }
                 */
                //$register->sendValueToNode($nodeIP, $message, $apikey, $nodeid, $timeout);
                //$register->attributeValueSetter($attributeUid, $nodeid);
            }
        } else {

            $log->warn("Json string sent to server has not been correctly formatted");
        }
    } else {
        $log->warn("Json string sent to server has not been correctly formatted");
    }
}

//Working Example of "Create" json string: http://localhost/OpenEMan/register/create.json?apikey=4903c8a630a99c63251b5a34ac043ba5&nodeMAC=01:23:45:67:89:ab&fromAddress=123.123.123.123&timeout=15
//Working Example of "setup" json string: http://localhost/OpenEMan/register/setup.json?apikey=7c399b2a696c8e1d3efebb7767fba593&node=50&json=55555566666677777&timeout=225
