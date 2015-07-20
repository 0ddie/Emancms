<?php

class register {

    private $mysqli;
    private $feed;
    private $log;

    //private $feed;
    //private $redis;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
        //$this->feed = $feed;
        //$this->redis = $redis
        //$this->log = new EmonLogger(__FILE__);
    }

    /*
     * Querys the db to see if the node you're trying to register exists
     */

    public function exists($nodeMAC,$userid) {
        
        
        $result = $this->mysqli->query("SELECT `MacAddress` FROM `Node_reg` WHERE `MacAddress` = '$nodeMAC'AND `userid` = '$userid'");
        if ($result->num_rows === 1) {
            $this->nodeMessage($nodeMAC,$userid);
            return 1;
        } else {
            return 0;
        }
    }

    /*
     * checks the length apikey
     */

    public function apikeycheck($apikey) {
        if (strlen($apikey) != 32) {

            return 1;
        } elseif ($this->correctApiKey($apikey) === 0) {

            return 2;
        }
    }

    /*
     * checks the apikey is the same as the one in the "users" table
     */

    function correctApiKey($apikey) {
        
        $result = $this->mysqli->query("SELECT apikey_write FROM users WHERE `apikey_write` = '$apikey'");
        if ($result->num_rows === 1) {
            return 1;
        } else {
            return 0;
        }
    }

    /*
     * Checks the IP is a valid IP
     */

    public function checkNodeIP($nodeIP) {
        if (!ip2long($nodeIP)) {
            return 1;
        }
    }

    /*
     * checks the MAC address is a valid one.
     */

    public function checkMACAddress($nodeMAC) {
        if (preg_match('/^([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}$/', $nodeMAC))
            return 0;
        else
            return 1;
    }

    /*
     * checks the node ID is a correct node ID
     */

    public function incorrectNodeID($nodeid, $userid) {
        
        $result = $this->mysqli->query("SELECT NodeID FROM Node_reg WHERE `NodeID` = '$nodeid' AND `userid` = '$userid'");
        //$result2 = $this->mysqli->query("SELECT userid FROM Node_reg WHERE `userid` = '$userid'");

        if ($result->num_rows === 1) {
        //if ($result2->num_rows === 1) {
        
            return 0;
        //}
        }
        else {
            //$thisError = "Wales!";
            //$this->$log->info($thisError);
            return 1;
        }
    }

    /*
     * checks the nodeid is constant (comparing the sent data to the returned data from the node)
     */

    public function nodeIDConstant($nodeid, $json, $nodeidL) {
        $NICOffset = 11;
        if (substr_compare($json, $nodeid, $NICOffset [$nodeidL[true]]) === 0) {
            return 1;
        }
    }

    /*
     * adds a node to the Node_Reg table
     */

    public function addNode($nodeMAC, $nodeIP, $userid) {
        
        $nodeid = $this->nodeIDIncrementer($userid);
        $this->mysqli->query("INSERT INTO `Node_reg` (`NodeID`, `FromAddress`, `MACAddress`, `userid`) VALUES ('$nodeid','$nodeIP','$nodeMAC','$userid')");
        $this->nodeMessage($nodeMAC, $userid);
        return $nodeid;
    }

    /*
     * Function to pull node id from table Node_reg
     */

    public function nodeMessage($nodeMAC, $userid) {
        
        $result = $this->mysqli->query("SELECT `NodeID` FROM `Node_reg` WHERE `MACAddress` LIKE '$nodeMAC'AND `userid` = '$userid'");
        //$result2 = $this->mysqli->query("SELECT `nodeIP` FROM 'Node_reg' WHERE `MacAddress` = '$nodeMAC'");
        $row = mysqli_fetch_row($result);
        $nodeid = $row[0];
        return $nodeid;
    }

    /*
     * Increments the nodeID 
     */

    public function nodeIDIncrementer($userid) {

        $result = $this->mysqli->query("SELECT MAX(NodeID) FROM `Node_reg` WHERE `userid` = '$userid'");
        $row = mysqli_fetch_row($result);
        $query = $row[0];
        
        $result2 = $this->mysqli->query("SELECT MAX(nodeid) FROM `input` WHERE `userid` = '$userid'");
        $row2 = mysqli_fetch_row($result2);
        $query2 = $row2[0];

        $lastNode = max($query, $query2);
        $nextNode = $lastNode + 1;
        return $nextNode;

    }

    /*
     * Parses the Json string and pulls out the values using comma's
     */

    public function jsonParse($json, $nodeid, $doing) {

        global $session;

        $firstcomma = strpos($json, ',', 0);
        $firstoffset = $firstcomma + 1;
        $secondcomma = strpos($json, ',', $firstoffset);
        $secondoffset = $secondcomma + 1;
        $thirdcomma = strpos($json, ',', $secondoffset);
        $thirdoffset = $thirdcomma + 1;
        $groupID = substr($json, 0, $firstcomma);
        $attributeID = substr($json, $firstoffset, ($secondcomma - $firstoffset));
        $attributeNumber = substr($json, $secondoffset, ($thirdcomma - $secondoffset));
        $attributeDefaultValue = substr($json, $thirdoffset);


        print_r("here 1 ");
        if ($this->checkGroupID($groupID) === 1) {
            return 1;
        } elseif ($this->checkGroupID($groupID) === 2) {
            return 1;
        } elseif ($this->checkGroupID($groupID) === 3) {
            return 2;
        }
                print_r("here 2 ");


        if ($this->checkAttributeID($attributeID) === 1) {
            return 3;
        } elseif ($this->checkAttributeID($attributeID) === 2) {
            return 3;
        } elseif ($this->checkAttributeID($attributeID) === 3) {
            return 4;
        }

        if ($this->checkInputAttributeNumber($attributeNumber) === 1) {
            return 5;
        } elseif ($this->checkInputAttributeNumber($attributeNumber) === 2) {
            return 6;
        }

        $userid = $session['userid'];

        /*
         * Save's the attributes to the table
         */
        if ($this->checkEverything($groupID, $attributeID, $attributeNumber, $nodeid, $userid) != 0) {
                return 7;
        }
        
        if ($this->checkUserID($userid, $nodeid)!=0){
            return 8;
        }


        if ($doing === 0) {
            $this->saveToAttributes($groupID, $attributeID, $attributeNumber, $attributeDefaultValue, $nodeid, $userid);
        }

        $attributeUid = $this->getEverything($groupID, $attributeID, $attributeNumber, $nodeid, $userid);



        return($groupID . $attributeID . $attributeNumber . $attributeDefaultValue . "-" . $attributeUid);
    }

    /*
     * Checks if the attribute is already registered
     */
    public function checkUserID($userid,$nodeid){
        $result = $this->mysqli->query("SELECT `nodeid` FROM `Node_reg` WHERE `nodeid` = '$nodeid' AND userid = '$userid'");
        if ($result->num_rows === 1) {
                return 0;
            }    
            else {return 1;
                }
    }
    public function checkEverything($groupID, $attributeID, $attributeNumber, $nodeid, $userid) {
        
        $result = $this->mysqli->query("SELECT `attributeUid` FROM `attributes` WHERE `nodeid` = '$nodeid' AND `groupid` = '$groupID' AND `attributeId` = '$attributeID' AND `attributeNumber` LIKE '$attributeNumber' AND `userid` = '$userid'");
                if ($result->num_rows === 1) {
                return 1;
            }    
            else {return 0;}
                }

    /*
     * get's the attribute Uid based on all the known information
     */

    public function getEverything($groupID, $attributeID, $attributeNumber, $nodeid, $userid) {
        
        $result = $this->mysqli->query("SELECT `attributeUid` FROM `attributes` WHERE `nodeid` = '$nodeid' AND `groupid` = '$groupID' AND `attributeId` = '$attributeID' AND `attributeNumber` LIKE '$attributeNumber' AND `userid` = '$userid'");
        $row = mysqli_fetch_row($result);
        $query = $row[0];
        return $query;
    }

    /*
     * Saves the imported attributes from Jsonparse into a table
     */

    public function saveToAttributes($groupID, $attributeID, $attributeNumber, $attributeDefaultValue, $nodeid, $userid) {
        print_r(" Attribute Values: Group Id:".$groupID." Attribute Id: ".$attributeID." Attribute Number: ".$attributeNumber." Attribute Default Value: ". $attributeDefaultValue." node id: ". $nodeid. " User id: ".$userid);
        $this->mysqli->query("INSERT INTO attributes (groupid,attributeId,attributeNumber,attributeDefaultValue,nodeid,userid) VALUES ('$groupID','$attributeID','$attributeNumber','$attributeDefaultValue','$nodeid','$userid')");
    }

    /*
     * Creates an Input
     */

    public function inputCreator($nodeid, $input, $reformattedJson) {

        global $session;

        $userid = $session['userid'];
        $name = ("N".$reformattedJson);

        $input->create_input($userid, $nodeid, $name);
    }

    /*
     * creates the feed
     */

    public function feedCreator($groupIDDesc, $attributeIDDesc, $attributeUid) {


        global $session, $feed;
        $userid = $session['userid'];
        $name = ($attributeUid . " This node is a " . $groupIDDesc . " Measuring " . $attributeIDDesc);
        //$name = "test19";
        $datatype = 1;
        $engine = 2;
        $options_in = NULL;
        $result = $feed->create($userid, $name, $datatype, $engine, $options_in);

        if ($result['success'] === false) {
            return array('content' => "Feed not created");
        }



        return $result['feedid'];

        //returns feed id
    }

    /*
     * returns the feed id
     */

    public function feed_id_getter() {
        

        $result = $this->mysqli->query("SELECT MAX(id) FROM `feeds`");
        $row = mysqli_fetch_row($result);
        $query = $row[0];
        return $query;
    }

    /*
     * changes the feed fields
     */

    public function set_feed_fields($id, $tag, $name) {
        
        $this->mysqli->query("UPDATE `feeds` SET `tag` = '$tag' WHERE `id` = '$id'");
        $this->mysqli->query("UPDATE `feeds` SET `name` = '$name' WHERE `id` = '$id'");
    }

    /*
     * starts the timer

      public function timer() {
      //Start timer ($timeTaken)
      $fTime = time();
      $sTime = time();
      }
      /*
     * Checks to see if the program has timed out

      public function timedOut($timeout, $timeTaken) {
      if ($timeout <= $timeTaken) {
      return 1;
      }
      }
      /*
     * Checks the group ID is correctly formatted and that it exists
     */

    public function checkGroupID($groupID) {
        
        if (strncmp($groupID, '0x0', 3) === 0) {
            preg_match('/^([a-fA-F0-9]){3}$/', substr($groupID, 3), $matches, PREG_OFFSET_CAPTURE);
            if (count($matches) > 1) {
                $result = $this->mysqli->query("SELECT `ID` FROM `groupids` WHERE `ID` = '$groupID'");
                if ($result->num_rows < 1) {

                    return 3;
                } else {

                    return 0;
                }
            } else {
                return 1;
            }
        } else {
            return 2;
        }
    }

    /*
     * Checks the attribute ID is correctly formatted and it exists
     */

    public function checkAttributeID($attributeID) {
        
        if (strncmp($attributeID, '0x0', 3) === 0) {
            preg_match('/^([a-fA-F0-9]){3}$/', substr($attributeID, 3), $matches2, PREG_OFFSET_CAPTURE);
            if (count($matches2) > 1) {
                $result = $this->mysqli->query("SELECT `Identifier` FROM `attribute_information` WHERE `Identifier` = '$attributeID'");
                if ($result->num_rows < 1) {

                    return 3;
                } else {
                    return 0;
                }
            } else {
                return 1;
            }
        } else {
            return 2;
        }
    }

    /*
     * Checks the attribute number is correctly formatted
     */

    public function checkInputAttributeNumber($attributeNumber) {
        if (strncmp($attributeNumber, '0x0', 3) === 0) {
            preg_match('/^([a-fA-F0-9]){2}$/', substr($attributeNumber, 3), $matches3, PREG_OFFSET_CAPTURE);
            if (count($matches3) > 1) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 2;
        }
    }

    /*
     * checks the input json is correctly formatted
     */

    public function correctInputJson($json, $nodeid, $userid) {
        
        $doing = 1;
        $reformattedJson = ($this->jsonParse($json, $nodeid, $doing));

        $result = $this->mysqli->query("SELECT name FROM input WHERE `name` = '$reformattedJson' AND `nodeid` = '$nodeid' AND `userid` = '$userid'");
        if ($result->num_rows > 0) {
            return 1;
        } else {

            return 0;
        }
    }

    /*
     * returns the input ID
     */

    public function inputIdGetter($reformattedJson) {
        $name = ("N".$reformattedJson);
        $result = $this->mysqli->query("SELECT `id` FROM input WHERE `name` = '$name'");
        $row = mysqli_fetch_row($result);
        $query = $row[0];
        return $query;
    }
/*
    public function startTimer() {
        $startTime = time();
        return $startTime;
    }

    public function timedOut($startTime, $ender, $timeout) {
        do {
            if (($startTime - time()) > $timeout) {
                return array('content' => "Request timed out");
            }
            echo 'Hola ';
        } while ($ender = 0);
    }

    /*
     * returns an associative array of associative arrays of nodes
     */

    public function getAttributesByNode($userid) {
        $attributesByNode = [];

        //if ($this->redis) {
        //ToDo
        //} else {
        $result = $this->mysqli->query("SELECT * FROM attributes WHERE `userid` = '$userid'");

        if ($result->num_rows > 0) {
            for ($i = 0; $row = (array) $result->fetch_object(); $i++) {
                if (!isset($attributesByNode[$row['nodeid']]))
                    $attributesByNode[$row['nodeid']] = array();
                array_push($attributesByNode[$row['nodeid']], $row);
            }
            return $attributesByNode;
        } else
            return false;
    }

    /*
     * group Id Description getter
     */

    public function groupIDDescGetter($attributeUid) {
        
        $result = $this->mysqli->query("SELECT `groupid` FROM `attributes` WHERE `attributeUid` = '$attributeUid' ");
        $row = mysqli_fetch_row($result);
        $groupid = $row[0];
        $result2 = $this->mysqli->query("SELECT `Name` FROM `groupids` WHERE `ID` = '$groupid'");
        $row2 = mysqli_fetch_row($result2);
        $Description = $row2[0];
        return $Description;
    }

    /*
     * attribute Id Description getter
     */

    public function attributeIDDescGetter($attributeUid) {
        
        $result = $this->mysqli->query("SELECT `attributeId` FROM `attributes` WHERE `attributeUid` = '$attributeUid' ");
        $row = mysqli_fetch_row($result);
        $attributeid = $row[0];
        $result2 = $this->mysqli->query("SELECT `Name` FROM `attribute_information` WHERE `Identifier` = '$attributeid'");
        $row2 = mysqli_fetch_row($result2);
        $Name = $row2[0];
        return $Name;
    }

    /*
     * updates the attributes table
     */

    public function updateAttributesTableForFeed($id, $attributeUid) {
        

        $this->mysqli->query("UPDATE `attributes` SET `feedId` = '$id' WHERE `attributeUid` = '$attributeUid'");
    }

    public function updateAttributesTableForInput($inputid, $attributeUid) {
        

        $this->mysqli->query("UPDATE `attributes` SET `inputId` = '$inputid' WHERE `attributeUid` = '$attributeUid'");
    }

    public function getNodeIP($nodeid) {
        
        $result = $this->mysqli->query("SELECT `FromAddress` FROM `Node_reg` WHERE `NodeID` ='$nodeid' ");
        $row = mysqli_fetch_row($result);
        $nodeIP = $row[0];
        return $nodeIP;
    }

    public function getInputID($attributeUid) {
        
        $result = $this->mysqli->query("SELECT `inputId` FROM `attributes` WHERE `attributeUid` = '$attributeUid'");
        $row = mysqli_fetch_row($result);
        $inputid = $row[0];
        return $inputid;
    }

    public function getName($inputid) {
        
        $result = $this->mysqli->query("SELECT `name` FROM `input` WHERE `id` = '$inputid'");
        $row = mysqli_fetch_row($result);
        $name = $row[0];
        return $name;
    }

    public function sendValueToNode(/* $nodeIP, */ $apikey, $nodeid, $message, $timeout, $status = NULL) {
        $time = microtime(true);
        $expire = $time + $timeout;
        $nodeIP = "127.0.0.1";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://" . $nodeIP . "/OpenEMan/test.php"); /* /post.json?apikey=[" . $apikey . "]&node=[" . $nodeid . "]&json={$message}&timeout=[$timeout]");
         */
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_exec($ch);
        curl_close($ch);
        return $httpCode;
    }

    /*
     * Fills the group ID table
     */

    public function groupIdFiller() {
        
        $this->mysqli->query("
        INSERT INTO `groupids` (`ID`, `Name`, `Description`, `UUID`) VALUES
('0x0000', 'Basic', '\r\nAttributes for determining basic information about a device, setting user device information such as location, and enabling a device.\r\n', 1),
('0x0001', 'Power configuration', '\r\n\r\nAttributes for determining more detailed information about a device’s power source(s), and for configuring under/over voltage alarms.\r\n\r\n', 2),
('0x0002', 'Device Temperature Configuration', '\r\nAttributes for determining information about a device’s internal temperature, and for configuring under/over temperature alarms.\r\n', 3),
('0x0003', 'Identify', '\r\nAttributes and commands for putting a device into Identification mode (e.g. flashing a light)\r\n', 4),
('0x0004', 'Groups', '\r\nAttributes and commands for group configuration and manipulation.\r\n', 5),
('0x0004', 'Scenes', '\r\nAttributes and commands for scene configuration and manipulation.\r\n', 6),
('0x0005', 'Scenes', '\r\nAttributes and commands for scene configuration and manipulation.\r\n', 7),
('0x0006', 'On/off', '\r\nAttributes and commands for switching devices between ‘On’ and ‘Off’ states.\r\n', 8),
('0x0007', 'On/off Switch Confguration', '\r\nAttributes and commands for configuring On/Off switching devices\r\n', 9),
('0x0007', 'On/off Switch Confguration', '\r\nAttributes and commands for configuring On/Off switching devices\r\n', 10),
('0x0007', 'On/off Switch Confguration', '\r\nAttributes and commands for configuring On/Off switching devices\r\n', 11),
('0x0008', 'Level Control', '\r\nAttributes and commands for controlling devices that can be set to a level between fully ‘On’ and fully ‘Off’.\r\n', 12),
('0x0009', 'Alarms', '\r\nAttributes and commands for sending notifications and configuring alarm functionality.\r\n', 13),
('0x000a', 'Time', '\r\nAttributes and commands that provide a basic interface to a real-time clock.\r\n', 14),
('0x000b', 'RSSI Location', '\r\nAttributes and commands that provide a means for exchanging location information and channel parameters among devices.\r\n', 15),
('0x000c', '\r\nAnalog Input (Basic)', '\r\n\r\nAn interface for reading the value of an analog measurement and accessing various characteristics of that measurement.\r\n', 16),
('0x000d', '\r\nAnalog Output (Basic)', '\r\nAn interface for setting the value of an analog output (typically to the environment) and accessing various characteristics of that value.\r\n', 17),
('0x000e', '\r\nAnalog Value (Basic)', '\r\nAn interface for setting an analog value, typically used as a control system parameter, and accessing various characteristics of that value.\r\n', 18),
('0x000f', '\r\nBinary Input (Basic)', '\r\nAn interface for reading the value of a binary measurement and accessing various characteristics of that measurement.\r\n', 19),
('0x000f', '\r\nBinary Input (Basic)', '\r\nAn interface for reading the value of a binary measurement and accessing various characteristics of that measurement.\r\n', 20),
('0x0010', '\r\nBinary Output (Basic)', '\r\nAn interface for setting the value of a binary output (typically to the environment) and accessing various characteristics of that value.\r\n\r\n', 21),
('0x0011', '\r\nBinary Value (Basic)', '\r\nAn interface for setting a binary value, typically used as a control system parameter, and accessing various characteristics of that value.\r\n', 22),
('0x0012', '\r\nB\r\nMultistate Input (Basic)', '\r\nAn interface for reading the value of a multistate measurement and accessing various characteristics of that measurement.\r\n', 23),
('0x0012', '\r\nB\r\nMultistate Input (Basic)', '\r\nAn interface for reading the value of a multistate measurement and accessing various characteristics of that measurement.\r\n', 24),
('0x0012', '\r\nMultistate Input (Basic)', '\r\nAn interface for reading the value of a multistate measurement and accessing various characteristics of that measurement.\r\n', 25),
('0x0012', '\r\nMultistate Input (Basic)', '\r\nAn interface for reading the value of a multistate measurement and accessing various characteristics of that measurement.\r\n', 26),
('0x0013', '\r\nMultistate Output (Basic)', '\r\nAn interface for setting the value of a multistate output (typically to the environment) and accessing various characteristics of that value.\r\n', 27),
('0x0014', '\r\nMultistate Value (Basic)', '\r\nAn interface for setting a multistate value, typically used as a control system parameter, and accessing various characteristics of that value.\r\n', 28),
('0x0015', '\r\nCommissioning', '\r\nAttributes and commands for commissioning and managing a ZigBee device.\r\n', 29),
('0x0016- 0x00ff', '-', '\r\nReserved.\r\n', 30),
('0x0100', 'Shade Configuration', '\r\nAttributes and commands for configuring a shade.\r\n', 31),
('0x0101', 'Door Lock', '\r\nAn interface for controlling a door lock.\r\n', 32),
('0x0101', 'Door Lock', '\r\nAn interface for controlling a door lock.\r\n', 33),
('0x0102 – 0x01ff\r\n', '-', '\r\nReserved\r\n', 34),
('0x0200', 'Pump Configuration and Control', '\r\nAn interface for configuring and controlling pumps.\r\n', 35),
('0x0201', 'Thermostat', '\r\nAn interface for configuring and controlling the functionality of a thermostat.\r\n', 36),
('0x0202\r\n', '\r\nFan Control\r\n', '\r\nAn interface for controlling a fan in a heating / cooling system.\r\n', 37),
('0x0203\r\n', '\r\n\r\nDehumidification Control\r\n', '\r\n\r\nAn interface for controlling dehumidification.\r\n', 38),
('0x0204\r\n', '\r\n\r\nThermostat User Interface Configuration\r\n\r\n', '\r\n\r\n\r\nAn interface for configuring the user interface of a thermostat (which may be remote from the thermostat).\r\n', 39),
('0x0205-0x02ff', '-', 'Reserved', 40),
('0x0300', 'Colour Control', 'Attributes and commands colour properties of a colour-capable light', 41),
('0x0301', 'Ballast Configuration', '\r\nAttributes and commands for configuring a lighting ballast\r\n', 42),
('0x0302-0x03ff', '-', '\r\nReserved.\r\n', 43),
('0x0400', '\r\nIlluminance measurement', '\r\nAttributes and commands for configuring the measurement of illuminance, and reporting illuminance measurements.\r\n', 44),
('0x0401', '\r\nIlluminance level sensing', '\r\nAttributes and commands for configuring the sensing of illuminance levels, and reporting whether illuminance is above, below, or on target.\r\n', 45),
('0x0402', '\r\nTemperature measurement', '\r\nAttributes and commands for configuring the measurement of temperature, and reporting temperature measurements.\r\n', 46),
('0x0403', '\r\nPressure', '\r\nAttributes and commands for configuring the measurement of pressure, and reporting pressure measurements.\r\n', 47),
('0x0403', '\r\nPressure Measurement', '\r\nAttributes and commands for configuring the measurement of pressure, and reporting pressure measurements.\r\n', 48),
('0x0404', '\r\nFlow Measurement', '\r\nAttributes and commands for configuring the measurement of flow, and reporting flow rates.\n\n', 49),
('0x0405', '\r\nRelative humidity measurement', '\r\nAttributes and commands for configuring the measurement of relative humidity, and reporting relative humidity measurements.\r\n', 50),
('0x0406', '\r\nOccupancy Sensing', '\r\nAttributes and commands for configuring occupancy sensing, and reporting occupancy status.\n\n', 51),
('0x0407-0x04ff', '-', '\r\nReserved.\r\n', 52),
('0x0407-0x04ff', '-', '\r\nReserved.\r\n', 53),
('0x0500', 'IAS Zone', '\r\nAttributes and commands for IAS security zone devices.\r\n', 54),
('0x0501', 'IAS ACE', '\r\nAttributes and commands for IAS Ancillary Control Equipment.\r\n', 55),
('0x0502', 'IAS WD', '\r\nAttributes and commands for IAS Warning Devices.\r\n', 56),
('0x0502', 'IAS WD', '\r\nAttributes and commands for IAS Warning Devices.\r\n', 57),
('0x0503 - 0x05ff', '-', '\r\nReserved.\r\n', 58),
('0x0600', 'Generic Tunnel', '\r\nThe minimum common commands and attributes required to tunnel any protocol.\r\n', 59),
('0x0601', 'BACnet Protocol Tunnel', '\r\nCommands and attributes required to tunnel the BACnet protocol.\r\n', 60),
('0x0602', '\r\nAnalog Input (BACnet Regular)\r\n', '\r\nAn interface for accessing a number of commonly used BACnet based attributes of an analog measurement.\r\n', 61),
('0x0603', '\r\nAnalog Input (BACnet Extended)\r\n', '\r\nAn interface for accessing a number of BACnet based attributes of an analog measurement.\r\n', 62),
('0x0604', '\r\nAnalog Output (BACnet Regular)\r\n', '\r\nAn interface for accessing a number of commonly used BACnet based attributes of an analog output.\r\n', 63),
('0x0605', '\r\nAnalog Output (BACnet Extended)\r\n', '\r\nAn interface for accessing a number of BACnet based attributes of an analog output.\r\n', 64),
('0x0606', '\r\nAnalog Value (BACnet Regular)\r\n', '\r\nAn interface for accessing a number of commonly used BACnet based attributes of an analog value, typically used as a control system parameter.\r\n', 65),
('0x0607', '\r\nAnalog Value (BACnet Extended)\r\n', '\r\nAn interface for accessing a number of BACnet based attributes of an analog value, typically used as a control system parameter.\r\n', 66),
('0x0608', '\r\nBinary Input (BACnet Regular)\r\n', '\r\nAn interface for accessing a number of commonly used BACnet based attributes of a binary measurement.\r\n', 67),
('0x0609', '\r\nBinary Input (BACnet Extended)\r\n', '\r\nAn interface for accessing a number of BACnet based attributes of a binary measurement.\r\n', 68),
('0x060a', '\r\nBinary Output (BACnet Regular)\r\n', '\r\nAn interface for accessing a number of commonly used BACnet based attributes of a binary output.\r\n', 69),
('0x060b', '\r\nBinary Output (BACnet Extended)\r\n', '\r\nAn interface for accessing a number of BACnet based attributes of a binary output.\r\n', 70),
('0x060c', '\r\nBinary Value (BACnet Regular)\r\n', '\r\nAn interface for accessing a number of commonly used BACnet based attributes of a binary value, typically used as a control system parameter.\r\n', 71),
('0x060d', '\r\nBinary Value (BACnet Extended)\r\n', '\r\nAn interface for accessing a number of BACnet based attributes of a binary value, typically used as a control system parameter.\r\n', 72),
('0x060e', '\r\nMultistate Input (BACnet Regular)\r\n', '\r\nAn interface for accessing a number of commonly used BACnet based attributes of a multistate measurement.\r\n', 73),
('0x060f', '\r\n\r\nMultistate Input (BACnet Extended)\r\n', '\r\n\r\nAn interface for accessing a number of BACnet based attributes of a multistate measurement.\r\n', 74),
('0x0610', '\r\nMultistate Output (BACnet Regular)\r\n', '\r\nAn interface for accessing a number of commonly used BACnet based attributes of a multistate output.\r\n', 75),
('0x0611', '\r\nMultistate Output (BACnet Extended\r\n', '\r\nAn interface for accessing a number of BACnet based attributes of a multistate output.\r\n', 76),
('0x0612', '\r\nMultistate Value (BACnet Regular)\r\n', '\r\nAn interface for accessing a number of commonly used BACnet based attributes of a multistate value, typically used as a control system parameter.\r\n', 77),
('0x0613', '\r\nMultistate Value (BACnet Extended)\r\n', '\r\n\r\nAn interface for accessing a number of BACnet based attributes of a multistate value, typically used as a control system parameter.\r\n', 78),
('0x0614-0x06ff', '\r\n-', '\r\nReserved', 79);
        ");
    }

    /*
     * fills the attributeId table
     */

    public function attributeIdFiller() {
        
        $this->mysqli->query("
                INSERT INTO `attribute_information` (`Identifier`, `GroupID`, `Name`, `Type`, `Min`, `Max`, `Default_Value`, `Mandatory/Optional`, `UUID`) VALUES
('0x0000', '0x0201', 'LocalTemperature', 'Signed 16-bit integer', 38221, 32767, '-', 1, 1),
('0x0001', '0x0201', 'OutdoorTemperature', 'Signed 16-bit integer', 0, 0, '-', 0, 3),
('0x0002', '0x0201', 'Ocupancy', '8-bit bitmap', 0, 0, '-', 0, 5),
('0x0003', '0x0201', 'AbsMinHeatSetpointLimit', 'Signed 16-bit integer', 0, 0, '7', 0, 6),
('0x0004', '0x0201', 'AbsMaxHeatSetpointLimit', 'Signed 16-bit integer', 0, 0, '30', 0, 7),
('0x0005', '0x0201', 'AbsMinCoolSetpointLimit', 'Signed 16-bit integer', 0, 0, '16', 0, 8),
('0x0006', '0x0201', 'AbsMaxCoolSetpointLimit', 'Signed 16-bit integer', 0, 0, '32', 0, 9),
('0x0007', '0x0201', 'PICoolingDemand', 'Unsigned 8-bit integer', 0, 0, '-', 0, 10),
('0x0008', '0x0201', 'PIHeatingDemand', 'Unsigned 8-bit integer', 0, 0, '-', 0, 11),
('0x0010', '0x0201', 'LocalTemperatureCalibration', 'Signed 8-bit integer', 0, 0, '0', 0, 12),
('0x0011', '0x0201', 'OccupiedCoolingSetpoint', 'Signed 16-bit integer', 0, 0, '26', 1, 13),
('0x0012', '0x0201', 'OccupiedHeatingSetpoint', 'Signed 16-bit integer', 0, 0, '20', 1, 14),
('0x0013', '0x0201', 'UnoccupiedCoolingSetpoint', 'Signed 16-bit integer', 0, 0, '26', 0, 15),
('0x0014', '0x0201', 'UnoccupiedHeatingSetpoint', 'Signed 16-bit integer', 0, 0, '20', 0, 18),
('0x0015', '0x0201', 'MinHeatSetpointLimit', 'Signed 16-bit integer', 0, 0, '7', 0, 21),
('0x0016', '0x0201', 'MaxHeatSetpointLimit', 'Signed 16-bit integer', 0, 0, '30', 0, 23),
('0x0017', '0x0201', 'MinCoolSetpointLimit', 'Signed 16-bit integer', 0, 0, '7', 0, 24),
('0x0018', '0x0201', 'MaxCoolSetpointLimit', 'Signed 16-bit integer', 0, 0, '30', 0, 25),
('0x0019', '0x0201', 'MinSetpointDeadBand', 'Signed 8-bit integer', 0, 0, '2.5', 0, 26),
('0x001a', '0x0201', 'RemoteSensing', '8-bit bitmap', 0, 0, '0', 0, 27),
('0x001b', '0x0201', 'ControlSequenceOfOperation', '8-bit enumeration', 0, 0, 'NULL', 1, 29),
('0x001c', '0x0201', 'SystemMode', '8-bit enumeration', 0, 0, 'NULL', 1, 30),
('0x001d', '0x0201', 'AlarmMask', '8-bit bitmap', 0, 0, 'NULL', 0, 32),
('0x0000', '0x0403', 'MeasuredValue', 'Signed 16-bit integer', 0, 0, '0', 1, 33),
('0x0001', '0x0403', 'MinMeasuredValue', 'Signed 16-bit integer', 0, 0, '-', 1, 34),
('0x0002', '0x0403', 'MaxMeasuredValue', 'Signed 16-bit integer', 0, 0, '-', 1, 35),
('0x0003', '0x0403', 'Tolerance', 'unSigned 16-bit integer', 0, 0, '-', 0, 36),
('0x0000', '0x0405', 'MeasuredValue', 'Unsigned 16-bit integer', 0, 0, '-', 1, 37),
('0x0001', '0x0405', 'MinMeasuredValue', 'Unsigned 16-bit integer', 0, 0, '-', 1, 38),
('0x0002', '0x0405', 'MaxMeasuredValue', 'Unsigned 16-bit integer', 0, 0, '-', 1, 39),
('0x0003', '0x0405', 'Tolerance', 'Unsigned 16-bit integer', 0, 0, '-', 0, 40);
               ");
    }

    /*
     * checks the tables to see if they're populated
     */

    public function tablesEmpty() {
        
        $result = $this->mysqli->query("SELECT * FROM `attribute_information` WHERE UUID = '1'");
        if ($result->num_rows === 1) {
            $result2 = $this->mysqli->query("SELECT * FROM `groupids` WHERE UUID = '1'");
            if ($result2->num_rows === 1) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    }

}

/*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    
