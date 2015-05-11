<?php

class register {

    private $mysqli;
    /*
     * Querys the db to see if the node you're trying to register exist
     */

    public function exists($nodeMAC) {
        global $mysqli;
        $result = $mysqli->query("SELECT FromAddress FROM Node_reg WHERE `FromAddress` = '$nodeMAC'");
        if ($result->num_rows === 1) {
            nodeMessage($nodeMAC);
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
     

    public function ipchecker() {
        if (!ip2long($nodeip)) {
            $this->misformedError();
            return 1;
        }
    }
     * 
     * 
     */
    public function checkMACAddress($nodeMAC){
        function is_valid_mac($nodeMAC)
    {   
    // 01:23:45:67:89:ab
    if (preg_match('/^([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}$/', $nodeMAC))
        return 1;
    // 01-23-45-67-89-ab
    if (preg_match('/^([a-fA-F0-9]{2}\-){5}[a-fA-F0-9]{2}$/', $nodeMAC))
        return 1;
    // 0123456789ab
    else if (preg_match('/^[a-fA-F0-9]{12}$/', $nodeMAC))
        return 1;
    // 0123.4567.89ab
    else if (preg_match('/^([a-fA-F0-9]{4}\.){2}[a-fA-F0-9]{4}$/', $nodeMAC))
        return 1;
    else
        return 0;
}
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
     * checks the json string is the right length
     */
    public function jsonStringError($json, $nodeid) {
        global $mysqli;
        $nodeidL = (strlen($nodeid));
        $jsonL = 11 + $nodeidL;
        if (strlen($json) != $jsonL) {
            return 1;
        } else {
            return 0;
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

    public function addNode($nodeMAC) {
        global $mysqli;
        $nodeid = $this->nodeIDIncrementer();
        $mysqli->query("INSERT INTO Node_reg (NodeID,FromAddress) VALUES ('$nodeid','$nodeMAC')");
        $this->nodeMessage($nodeMAC);
        print_r("Node added to Node_reg");
    }

    /*
     * Function to pull node id from table Node_reg
     */

    public function nodeMessage($nodeMAC) {
        global $mysqli;
        $result = $mysqli->query("SELECT NodeID FROM `Node_reg` WHERE `FromAddress` = '$nodeMAC' ");
        $row = mysqli_fetch_assoc($result);
        print_r($row);
    }
    /*
     * Increments the nodeID 
     */
    public function nodeIDIncrementer(){
        global $mysqli;
        $lastnodeid = $mysqli->query("SELECT MAX (NodeID) FROM 'Node_reg'");
        $nodeid = $lastnodeid + 1;
        return $nodeid;
        
    }
    
   /*
    * Pulls the MAC Address from the node and adds it to the table
    */
    public function MACAddress()
    {
        
    }
    public function jsonParse($json,$nodeid){
        $firstcomma = strpos( $json , ',' , 0 );
        $firstoffset= $firstcomma + 1;
        $secondcomma = strpos( $json, ',' , $firstoffset);
        $secondoffset = $secondcomma + 1;
        $thirdcomma = strpos( $json, ',' , $secondoffset);
        $thirdoffset = $thirdcomma+1;
        $groupID=substr($json, 0, $firstcomma);
        print_r($groupID);
        print_r(",");
        $attributeID=substr($json, $firstoffset, ($secondcomma - $firstoffset));
        print_r($attributeID);
        print_r(",");
        $attributeNumber=substr($json, $secondoffset, ($thirdcomma - $secondoffset));
        print_r($attributeNumber);
        print_r(",");
        $attributeDefaultValue=  substr($json, $thirdoffset);
        print_r($attributeDefaultValue);
        $this->saveToAttributes($groupID,$attributeID,$attributeNumber,$attributeDefaultValue,$nodeid);
        return 1;
        }
        /* I think this should be a whole other module.
         * What i've just written resembles a controller class
         * Attributes registration module maybe?
         */
    public function checkAttributeNumber($attributeNumber){
        global $mysqli;
        $result = $mysqli->query("SELECT NodeID FROM Node_reg WHERE `NodeID` = '$nodeid'");
        if ($result->num_rows === 1) {
            return 0;
        } else {

            $this->misformedError();
            return 1;
        }
        /*
         * I want to do a bug check to check if the groupID is valid.
         * Surely there's a better way than writing them all in a hash table then calling them?
         */
    }
    public function saveToAttributes($groupID,$attributeID,$attributeNumber,$attributeDefaultValue,$nodeid){
         global $mysqli;
        $mysqli->query("INSERT INTO attributes (groupid,attributeId,attributeNumber,attributeDefaultValue,nodeid) VALUES ('$groupID','$attributeID','$attributeNumber','$attributeDefaultValue','$nodeid)");
        print_r("Attribute added to attributes");
    }

    public function inputcreator(){
        global $mysqli;
        $result = $mysqli->query("SELECT ");
        /*
         * Need to import userid, nodeid and name to here. Should name be groupid?
         */
        
    }
    
    }
    

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

