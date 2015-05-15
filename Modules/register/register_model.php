<?php

class register {

    private $mysqli;

    /*
     * Querys the db to see if the node you're trying to register exists
     */

    public function exists($nodeMAC) {
        global $mysqli;
        $result = $mysqli->query("SELECT MacAddress FROM Node_reg WHERE `MacAddress` = '$nodeMAC'");
        if ($result->num_rows === 1) {
            $this->nodeMessage($nodeMAC);
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
            misformedError();

            return 1;
        } elseif ($this->correctApiKey($apikey) === 0) {

            return 2;
        }
    }

    /*
     * checks the apikey is the same as the one in the "users" table
     */

    function correctApiKey($apikey) {
        global $mysqli;
        $result = $mysqli->query("SELECT apikey_write FROM users WHERE `apikey_write` = '$apikey'");
        if ($result->num_rows === 1) {
            return 1;
        } else {
            $this->misformedError();
            return 0;
        }
    }

    /*
     * Checks the IP is a valid IP
     */

      public function checkNodeIP($nodeIP) {
      if (!ip2long($nodeIP)) {
      $this->misformedError();
      return 1;
      }
      }
  
      /*
       * checks the MAC address is a valid one.
       */
   

        public function checkMACAddress($nodeMAC) {
            // 01:23:45:67:89:ab
            if (preg_match('/^([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}$/', $nodeMAC))
                return 0;

            else
                return 1;
        }

    

    /*
     * checks the node ID is a correct node ID
     */

    public function correctNodeID($nodeid) {
        global $mysqli;
        $result = $mysqli->query("SELECT NodeID FROM Node_reg WHERE `NodeID` = '$nodeid'");
        if ($result->num_rows === 1) {
            return 0;
        } else {

            $this->misformedError();
            return 1;
        }
    }

    /*
     * checks the nodeid is constant (comparing the sent data to the returned data from the node)
     */

    public function nodeIDConstant($nodeid, $json, $nodeidL) {
        $NICOffset = 11;
        if (substr_compare($json, $nodeid, $NICOffset [$nodeidL[true]]) === 0) {
            print_r("Incorrectly formatted json");
            return 1;
        }
    }

    /*
     * Function to print when these functions find an error
     */

    public function misformedError() {
        print_r("Json string misformed ");
    }

    /*
     * adds a node to the Node_Reg table
     */

    public function addNode($nodeMAC,$nodeIP) {
        global $mysqli;
        print_r("got here");
        $nodeid = $this->nodeIDIncrementer();
        $mysqli->query("INSERT INTO `OpenEMan`.`Node_reg` (`NodeID`, `FromAddress`, `MACAddress`) VALUES ('$nodeid','$nodeIP','$nodeMAC')");
        $this->nodeMessage($nodeMAC);
        print_r("Node added to Node_reg");
    }

    /*
     * Function to pull node id from table Node_reg
     */

    public function nodeMessage($nodeMAC) {
        global $mysqli;
        $result = $mysqli->query("SELECT NodeID FROM `Node_reg` WHERE `MacAddress` = '$nodeMAC' ");
        //print_r($result);
        $result2 = $mysqli->query("SELECT nodeIP FROM 'Node_reg' WHERE 'MacAddress' = '$nodeMAC'");
        //print_r($result2); 
    }

    /*
     * Increments the nodeID 
     */

    public function nodeIDIncrementer() {
        global $mysqli;
        $query = $mysqli->query("SELECT MAX(NodeID) FROM `Node_reg`;");
        print_r($query);
        //$version = $st[0] + 1;
//        $sel = $mysqli->query("SELECT MAX(NodeID) FROM `Node_reg`;");
//        $sel_row = $sel->fetch_object();
//        $result = $sel_row->NodeID;
        $result = $mysqli->query($query);
        $followingdata = $result->fetch_assoc();
        get_object_vars($followingdata);
        //print_r ($result[0]);
        //return $nodeid;

    }

    public function jsonParse($json, $nodeid) {
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
        
        print_r($groupID.",".$attributeID.",".$attributeNumber.",".$attributeDefaultValue);
        
         if($this->checkGroupID($groupID)===1){
                print_r ("Not in Group ID range");
         }elseif($this->checkGroupID($groupID)===2){
                print_r ("Not a correctly formatted group ID");
         }
         if($this->checkAttributeID($attributeID)===1){
                print_r ("Not in Attribute range");
         }elseif($this->checkAttributeID($attributeID)===2){
                print_r ("Not a correctly formatted Attribute ID");
         }   
         
         if($this->checkInputAttributeNumber($attributeNumber)===1){
                print_r ("Not in Attribute Number range");
         }elseif($this->checkInputAttributeNumber($attributeNumber)===2){
                print_r ("Not a correctly formatted Attribute Number");
         }  
         
        $this->saveToAttributes($groupID, $attributeID, $attributeNumber, $attributeDefaultValue, $nodeid);
        return 1;
    }


    public function checkAttributeNumber($attributeNumber) {
        global $mysqli;
        $result = $mysqli->query("SELECT NodeID FROM Node_reg WHERE `NodeID` = '$nodeid'");
        if ($result->num_rows === 1) {
            return 0;
        } else {

            $this->misformedError();
            return 1;
        }
 
    }

    public function saveToAttributes($groupID, $attributeID, $attributeNumber, $attributeDefaultValue, $nodeid) {
        global $mysqli;
        $mysqli->query("INSERT INTO attributes (groupid,attributeId,attributeNumber,attributeDefaultValue,nodeid) VALUES ('$groupID','$attributeID','$attributeNumber','$attributeDefaultValue','$nodeid)");
        print_r("Attribute added to attributes");
    }

    public function inputCreator($nodeid, $json, $input) {
      
        global  $session;
        
        $userid = $session['userid'];
        $name = $json;
        
        $input->create_input($userid, $nodeid, $name);
    }
    
    public function feedCreator($json){
        global $feed;
         $userid = $session['userid'];
         $name = $json;
         /*
          * Data type, engine, options in???
          */
        $feed->create($userid,$name,$datatype,$engine,$options_in);
    }
  
    /*
     * starts the timer
     */
    public function timer() {
        //Start timer ($timeTaken)
        $fTime = time();
        $sTime = time();
    }
    /*
     * Checks to see if the program has timed out
     */
    public function timedOut($timeout, $timeTaken) {
        if ($timeout <= $timeTaken) {
            return 1;
        }
    }

    public function checkGroupID($groupID) {

       if (strncmp ($groupID, '0x0', 3)===0){
           preg_match('/^([a-fA-F0-9]){3}$/', substr($groupID,3), $matches, PREG_OFFSET_CAPTURE);
           if (count($matches)>1){
               return 0;
           }else{return 1;}
    }else{return 2;}
 }
     public function checkAttributeID($attributeID) {

       if (strncmp ($attributeID, '0x0', 3)===0){
           preg_match('/^([a-fA-F0-9]){3}$/', substr($attributeID,3), $matches2, PREG_OFFSET_CAPTURE);
           if (count($matches2)>1){
               return 0;
           }else{return 1;}
    }else{return 2;}
 }
    public function checkInputAttributeNumber($attributeNumber){
         if (strncmp ($attributeNumber, '0x0', 3)===0){
           preg_match('/^([a-fA-F0-9]){2}$/', substr($attributeNumber,3), $matches3, PREG_OFFSET_CAPTURE);
           if (count($matches3)>1){
               return 0;
           }else{return 1;}
    }else{return 2;}
    }
    public function correctInputJson($json) {
        global $mysqli;
        $result = $mysqli->query("SELECT name FROM input WHERE `name` = '$json'");
        if ($result->field_count === 1) {
            return 1;
        
            
        } else {

            //$this->misformedError();
            return 0;
        }
        
           }
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

