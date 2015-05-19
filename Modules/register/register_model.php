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
            $this->misformedError();

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
        print_r($nodeid.$nodeIP.$nodeMAC);
        $mysqli->query("INSERT INTO `Node_reg` (`NodeID`, `FromAddress`, `MACAddress`) VALUES ('$nodeid','$nodeIP','$nodeMAC')");
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
        $result = $mysqli->query("SELECT MAX(NodeID) FROM `Node_reg`");
        $row = mysqli_fetch_row ( $result );
        $query = $row[0];
        print_r ($query);
        $nextnode =$query + 1;
        return $nextnode;


    }
    /*
     * Parses the Json string and pulls out the values using comma's
     */
    
    public function jsonParse($json, $nodeid, $doing) {
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
        
        //print_r($groupID.",".$attributeID.",".$attributeNumber.",".$attributeDefaultValue);
        
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
         /*
          * Save's the attributes to the table
          */
        
         if ($doing === 0){
        $this->saveToAttributes($groupID, $attributeID, $attributeNumber, $attributeDefaultValue, $nodeid);
         }
         
         return($groupID.$attributeID.$attributeNumber.$attributeDefaultValue);
    }

/*
 * Checks the attribute number is correctly formatted
 * 
 */
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
/*
 * Saves the imported attributes from Jsonparse into a table
 */
    public function saveToAttributes($groupID, $attributeID, $attributeNumber, $attributeDefaultValue, $nodeid) {
        global $mysqli;
        $mysqli->query("INSERT INTO attributes (groupid,attributeId,attributeNumber,attributeDefaultValue,nodeid) VALUES ('$groupID','$attributeID','$attributeNumber','$attributeDefaultValue','$nodeid')");
        print_r($mysqli->error);
        print_r("Attribute added to attributes");
    }
/*
 * Creates an Input
 */
    public function inputCreator($nodeid, $input, $reformattedJson) {
      
        global  $session;
        
        $userid = $session['userid'];
        $name = $reformattedJson;
        print_r($reformattedJson);
        
        $input->create_input($userid, $nodeid, $name);
    }
/*
 * Create's a feed
 */
    public function feedCreator($reformattedJson){
        
        global $feed, $session, $redis, $mysqli, $feed_settings;
        include "Modules/feed/feed_model.php";
        $feed = new Feed($mysqli,$redis,$feed_settings);
        
        $userid = $session['userid'];
        $name = $reformattedJson;
        $datatype = 1;
        $engine = 2;
        $options_in = NULL;
        $feed->create($userid,$name,$datatype,$engine,$options_in);
        print_r ("Feed Created");
    }
    public function feed_id_getter(){
                global $mysqli;

        $result = $mysqli->query("SELECT MAX(id) FROM `feeds`");
        $row = mysqli_fetch_row ( $result );
        $query = $row[0];
        return $query;
    }
    public function set_feed_fields($id,$tag){
        global $mysqli;
        $this->mysqli->query("UPDATE feeds SET ".$tag." WHERE `id` = '$id'");
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
        /*
         * Checks the group ID is correctly formatted
         */
    public function checkGroupID($groupID) {

       if (strncmp ($groupID, '0x0', 3)===0){
           preg_match('/^([a-fA-F0-9]){3}$/', substr($groupID,3), $matches, PREG_OFFSET_CAPTURE);
           if (count($matches)>1){
               return 0;
           }else{return 1;}
    }else{return 2;}
 }
          /*
          * Checks the attribute ID is correctly formatted
          */
     public function checkAttributeID($attributeID) {

       if (strncmp ($attributeID, '0x0', 3)===0){
           preg_match('/^([a-fA-F0-9]){3}$/', substr($attributeID,3), $matches2, PREG_OFFSET_CAPTURE);
           if (count($matches2)>1){
               return 0;
           }else{return 1;}
    }else{return 2;}
 }
          /*
          * Checks the attribute number is correctly formatted
          */
    public function checkInputAttributeNumber($attributeNumber){
         if (strncmp ($attributeNumber, '0x0', 3)===0){
           preg_match('/^([a-fA-F0-9]){2}$/', substr($attributeNumber,3), $matches3, PREG_OFFSET_CAPTURE);
           if (count($matches3)>1){
               return 0;
           }else{return 1;}
    }else{return 2;}
    }
    /*
     * checks the input json is correctly formatted
     */
    
    public function correctInputJson($json,$nodeid){
        //print_r($json);
        global $mysqli;
        $doing = 1;
        $reformattedJson=($this->jsonParse($json, $nodeid, $doing));
       
        $result = $mysqli->query("SELECT name FROM input WHERE `name` = '$reformattedJson'");
        //print_r($result);
        if ($result->num_rows > 0) {
            return 1;
        
            
        } else {

            //$this->misformedError();
            return 0;
        }
        
           }
}


