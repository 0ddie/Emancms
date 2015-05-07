<?php

class register {

    private $mysqli;
    /*
     * Querys the db to see if the node you're trying to register exist
     */

    public function exists($nodeip) {
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

    public function ipchecker($nodeip) {
        if (!ip2long($nodeip)) {
            $this->misformedError();
            return 1;
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

    public function addNode($nodeip) {
        global $mysqli;
        $nodeid = $this->nodeIDIncrementer();
        $mysqli->query("INSERT INTO Node_reg (NodeID,FromAddress) VALUES ('$nodeid','$nodeip')");
        $this->nodeMessage($nodeip);
        print_r("Node added to Node_reg");
    }

    /*
     * Function to pull node id from table Node_reg
     */

    public function nodeMessage($nodeip) {
        global $mysqli;
        $result = $mysqli->query("SELECT NodeID FROM `Node_reg` WHERE `FromAddress` = '$nodeip' ");
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
    public function jsonParse($json){
        $firstcomma = strpos( $json , ',' , 0 );
        $firstoffset= $firstcomma + 1;
        $secondcomma = strpos( $json, ',' , $firstoffset);
        $secondoffset = $secondcomma + 1;
        $thirdcomma = strpos( $json, ',' , $secondoffset);
        $thirdoffset = $thirdcomma+1;
        $fourthcomma = strpos( $json, ',', $thirdoffset);
        $firstone=substr($json, 0, $firstcomma);
        print_r($firstone);
        print_r("       ");
        $secondone=substr($json, $firstoffset, $secondoffset);
        print_r($secondone);
        print_r("       ");
        $thirdone=substr($json, $secondoffset, $thirdcomma);
        print_r($thirdone);
        print_r("       ");
        $fourthone=  substr($json, $thirdoffset, $fourthcomma);
        print_r($fourthone);
       
        
        }
}

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

