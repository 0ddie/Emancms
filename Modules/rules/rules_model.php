<?php

/*
  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org
  ---------------------------------------------------------------------
  OEMan - Open Energy Management system for the OpenEnergyMonitor
  Developed by the Centre for Alternative Technology
  http://cat.org.uk

 */

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class Rules {

    private $mysqli;
    private $redis;

    public function __construct($mysqli, $redis) {
        $this->mysqli = $mysqli;
        $this->redis = $redis;
    }

    /*     * ************************************************* */
    /* Methods to check if server has just been turned 
     * on and to load the tables in redis
     * ************************************************** */

//have a look in feeds_model: load_to_redis($userid)
    public function first_run() {
// ToDo
        return false;
    }

    public function load_tables_into_redis() {
//ToDo    
    }

    /*     * *************************************** */
    /*   Get Rules                             */
    /*     * **************************************** */

    public function get_rules($userid) {
        if ($this->redis) {
            return $this->redis_get_rules($userid);
        } else {
            return $this->mysql_get_rules($userid);
        }
    }

    public function redis_get_rules($userid) {
//ToDo
    }

    public function mysql_get_rules($userid) {
        $userid = (int) $userid;
        $array_of_rules = array();
        $result = $this->mysqli->query("SELECT * FROM rules WHERE `userid` = '$userid'");
        for ($i = 0; $row = (array) $result->fetch_object(); $i++) {
            $array_of_rules[$i] = $row;
        }
        return $array_of_rules;
    }

    public function get_rule($ruleid, $userid) {
        if ($this->redis) {
            return $this->redis_get_rule($ruleid, $userid);
        } else {
            return $this->mysql_get_rule($ruleid, $userid);
        }
    }

    public function redis_get_rule($ruleid, $userid) {
//ToDo
    }

    public function mysql_get_rule($ruleid, $userid) {
        $userid = (int) $userid;
        $result = $this->mysqli->query("SELECT * FROM rules WHERE `userid` = '$userid' AND `ruleid`='$ruleid'");
        if ($result->num_rows > 0)
            return $result->fetch_array();
        else
            return false;
    }

    /*     * *************************************** */
    /*  Delete Rules                             */
    /*     * **************************************** */

    public function delete_rule($ruleid, $userid) {
        $ruleid = (int) $ruleid;
//check if rule exists
        if ($this->rule_exists($ruleid) == true) {
            $rule_deleted = $this->mysqli->query("DELETE FROM rules WHERE `ruleid` = '$ruleid' AND `userid`='$userid'");
            if ($this->redis && $rule_deleted) {
//ToDo delete rule
            }
            return $rule_deleted;
        } else {//if rule not found in database
            return $rule_deleted = false;
        }
    }

    /*     * *************************************** */
    /*  Save Rules                             */
    /*     * **************************************** */

    public function save_rule($attributes) {
        global $session;
        $ruleid = (int) $attributes['ruleid'];
        $userid = $session['userid'];
        $name = preg_replace('/[^\w\s-.]/', '', $attributes['name']);
        $description = preg_replace('/[^\w\s-.]/', '', $attributes['description']);
        $run_on = (preg_replace('/([^0-9\-: ])/', '', $attributes['run_on']));
//$run_on = $attributes['run_on'];
        $expiry_date = preg_replace('/([^0-9\-: ])/', '', $attributes['expiry_date']);
        $frequency = (int) $attributes['frequency'];
        $blocks = preg_replace('/[^\w\s-.\/<>"=]/', '', $attributes['blocks']);
        $enabled = $attributes['enabled'] == 'true' ? 1 : 0;

        if ($this->rule_exists($attributes['ruleid']) == false) {
            $rule_saved = $this->mysqli->query("INSERT INTO `rules` (`userid`, `name`, `description`, `run_on`, `expiry_date`, `frequency`, `blocks`,`enabled`) VALUES ('$userid', '$name', '$description', '$run_on', '$expiry_date', '$frequency', '$blocks','$enabled')");
            if ($this->redis && $rule_saved) {
//ToDo insert rule
            }
            if ($rule_saved == false || $rule_saved == 0)
                return 0;
            else
                return $this->mysqli->insert_id;
        } else {
            $rule_saved = $this->mysqli->query("UPDATE `rules` SET `name`='$name', `description`='$description', `run_on`='$run_on', `expiry_date`='$expiry_date', `frequency`='$frequency', `blocks`='$blocks', `enabled`='$enabled' WHERE `ruleid`= '$ruleid' AND `userid` = '$userid'");
            if ($this->redis && $rule_saved) {
//ToDo update rule
            }
            if ($rule_saved == false || $rule_saved == 0)
                return 0;
            else
                return $ruleid;
        }
    }

    /*     * *************************** */
    /*  Other methods             */
    /*     * *************************** */

    public function rule_exists($ruleid) {
        if ($this->redis) {
//ToDo check if rule exists
        } else {
            $query_result = $this->mysqli->query("SELECT ruleid FROM rules WHERE `ruleid` = '$ruleid'");
        }
        if ($query_result->num_rows > 0)
            return true;
        else
            return false;
    }

    public function add_mock_rules() {
        $this->mysqli->query("INSERT INTO `rules` (`ruleid`, `userid`, `name`, `description`, `run_on`, `expiry_date`, `frequency`, `blocks`) VALUES (NULL, '1', 'name', 'description', '2015-05-28 00:00:00', '2015-05-29 00:00:00', '300', 'bocks'), (NULL, '1', 'another', 'another', '2015-05-29 00:00:00', '2015-05-29 00:00:00', '300', 'another')");
    }

    public function getAttributesByNode($userid) {
        $attributesByNode = [];

        if ($this->redis) {
//ToDo
        } else {
            $result = $this->mysqli->query("SELECT * FROM attributes WHERE `userid` = '$userid'");

            if ($result->num_rows > 0) {
                for ($i = 0; $row = (array) $result->fetch_object(); $i++) {
                    $attributesByNode[$row['nodeid']] = $row;
                }
                return $attributesByNode;
            } else
                return false;
        }
    }

    public function disableRule($ruleid) {
        $ruleid = (int) $ruleid;
        $result = $this->mysqli->query("UPDATE `rules` SET `enabled`='0' WHERE `ruleid`= '$ruleid'");
        if ($this->redis) {
//ToDo
        }
        return $result;
    }

    public function setRunOn($ruleid, $new_run_on_date) {
        $ruleid = (int) $ruleid;
        $result = $this->mysqli->query("UPDATE `rules` SET `run_on`='$new_run_on_date' WHERE `ruleid`= '$ruleid'");
        if ($this->redis) {
//ToDo
        }
        return $result;
    }

    public function addPendingAck($type, $ruleid, $next_stage, $args, $timeout) {
        $time = time();
        if ($this->redis) {
//ToDo
        } else {
            $this->mysqli->query("INSERT INTO `rules` (`request_time`, `type`, `ruleid`, `next_stage`, `args`, `timeout`) VALUES ('$time',$type', '$ruleid', '$next_stage', '$args', '$timeout')");
        }
    }

    public function get_user_feeds_by_tag($userid) {
        global $feed_settings;
        include "Modules/feed/feed_model.php";

        $feed = new Feed($this->mysqli, $this->redis, $feed_settings);

        $array_of_feeds_by_tag = array();
        $array_of_user_feeds = $feed->get_user_feeds($userid); // We use this one to get the name and tag of each feed

        foreach ($array_of_user_feeds as $feedid => $feed_in_foreach) {
            if ($feed_in_foreach['tag'] == '')
                $feed_in_foreach['tag'] = 'No tag';
            if (!isset($array_of_feeds_by_tag[$feed_in_foreach['tag']])) { // If this is the first time we find this node
                $array_of_feeds_by_tag[$feed_in_foreach['tag']] = array();
            }
            array_push($array_of_feeds_by_tag[$feed_in_foreach['tag']], $feed_in_foreach);
        }
        return($array_of_feeds_by_tag);
    }

    /* End of other methods  */

    /* stagesToPhp()  */

    public function stagesToPhp($rule, $stage_to_run) {
        $blocks_string = $rule['blocks'];
        ob_start();

// 1) Declare variables
        $variables_string = $this->getBlocksString($blocks_string, 'variables');
        $variables = new SimpleXMLElement($variables_string);
        echo "/* Variables */<br/>";
        echo '$timeout = 60; /* default value for timeout when we wait for apendinng acks */<br/>';
        echo '$ruleid = ' . $rule['ruleid'] . ';<br/>';
        foreach ($variables->script as $var) {
//print_r($var->block);
//echo '$' . $var->block['var'] . ';<br/>';
            echo $this->blocksToPhp($var->block) . ';<br/>';
        }
        echo '<br/>';

// 2) Declare variables that hold feeds ids
        $feeds_ids_string = $this->getBlocksString($blocks_string, 'feeds');
        $feeds_ids = new SimpleXMLElement($feeds_ids_string);
        echo "/* Feed ids */<br/>";
        foreach ($feeds_ids->script as $id) {
//print_r($var->block);
            echo $this->blocksToPhp($id->block) . ' = (int) ' . $this->getVariableIdFromBlock($id->block) . ';<br/>';
        }
        echo '<br/>';

// 3) Declare variables that hold attributes ids
        $attributes_ids_string = $this->getBlocksString($blocks_string, 'attributes');
        $attributes_ids = new SimpleXMLElement($attributes_ids_string);
        echo "/* Attributes ids */<br/>";
        foreach ($attributes_ids->script as $id) {
//print_r($var->block);
            echo $this->blocksToPhp($id->block) . ' = (int) ' . $this->getVariableIdFromBlock($id->block) . ';<br/>';
        }
        echo '<br/>';

// 4) Switch for the Stages
        $stages_string = $this->getBlocksString($blocks_string, 'stages');
        $stages = new SimpleXMLElement($stages_string);
        echo '/* Run the current stage */<br/>';
        echo '$stage = ' . (int) $stage_to_run . ';<br/>';
        echo ('switch($stage){ <br/>' );
        foreach ($stages->script as $stage) {
//print_r($stage);
            if ($stage->block[0]['s'] == 'Stage' && $stage->block[0]->l != '') { // Check this block starts with a "Stage" block and that the number of the stage is not empty
                echo ' case ' . (int) $stage->block[0]->l . ':<br/>';
                for ($index = 1; $stage->block[$index]; $index++) {
                    echo $this->blocksToPhp($stage->block[$index]) . ';';
                }
                echo '  break;<br/>';
            }
        }
        echo ' default:<br/>  $ruleid = $rule["ruleid"];<br/>  $log->warn("Wrong stage in rule ' . '$ruleid' . ' - Stage: ' . $stage->block[0]->l . '");<br/> break;<br/>}';

        $php_code_for_web = ob_get_contents(); // $php_code_for_web uses <br/> for breaking lines
        ob_clean();
        return $php_code_for_web;
    }

    public function getBlocksString($blocks_string, $script_name) {
        $begining_of_slice = stripos($blocks_string, '<' . $script_name . '>');
        $end_of_slice = stripos($blocks_string, '</' . $script_name . '>') + strlen($script_name) + 3;
        return substr($blocks_string, $begining_of_slice, $end_of_slice - $begining_of_slice); // returns something like: <attributes><script x="10" y="35"><block var="1212120"/></script><script x="72" y="35"><block var="0x06500x06500x065100"/></script></attributes>
    }

    public function blocksToPhp($block) {
        if ($block['s']) { // if the block is command
            switch ($block['s']) {
                case 'doIf':
                    return '   if(' . $this->blocksToPhp($block->block) . '){<br/>   ' . $this->blocksToPhp($block->script) . '}<br/>';
                    break;
                case 'doIfElse':
//print_r($block->script);
                    $statement = '   if(' . $this->blocksToPhp($block->block) . '){<br/>     ' . $this->blocksToPhp($block->script[0]) . '<br/>   }<br/>';
                    $statement .= '   else{<br/>     ' . $this->blocksToPhp($block->script[1]) . '   }<br/>';
                    return $statement;
                    break;
                case 'requestFeed':
                    $feed_id = isset($block->l) ? (int) $block->l : $this->blocksToPhp($block->block);
                    /* echo '<pre>';
                      print_r($block);
                      echo '</pre>'; */
                    $statement = '/* $register->sendRequestToNode()*/</br>';
                    $statement .= '$rules->addPendingAck("requestFeed", $ruleid, $stage + 1, ["feedid"=>' . $feed_id . '], $timeout);</br>';
                    //$statement .= 
                    return $statement;
                    break;
                case 'setAttribute':
                    $parameters = $this->getBlockArguments($block); // $parameters[0] is AttributeUid and $parameters[1] is the value
                    $statement = '/* $register->sendValueToNode() */<br/>';
                    $statement .= '$rules->addPendingAck("setAttribute", $ruleid, $stage + 1, ["attributeUid"=>' . $parameters[0] . '], $timeout);</br>';
                    return $statement;
                    break;
                case 'getLastFeed':
                    $feed_id = isset($block->l) ? (int) $block->l : $this->blocksToPhp($block->block);
                    $statement = '$feed->get(' . $feed_id . ')</br>';
                    return $statement;
                    break;
                case 'reportLessThan': // This is an 'operator' command
                    $parameters = $this->getBlockArguments($block);
                    return "($parameters[0]) < ($parameters[1])";
                    break;
                case 'reportEquals': // This is an 'operator' command
                    $parameters = $this->getBlockArguments($block);
                    return "($parameters[0]) == ($parameters[1])";
                    break;
                case 'reportGreaterThan': // This is an 'operator' command
                    $parameters = $this->getBlockArguments($block);
                    return "($parameters[0]) > ($parameters[1])";
                    break;
                default:
                    return ' $log->warn("Command block not recognized: ' . $block['s'] . '");<br/>';
                    break;
            }
        } else if ($block['var']) { //this returns the name of a variable
            $var_name = '$' . preg_replace('/[^a-zA-Z0-9_]/', '', $block['var']);
            if (is_numeric(substr($var_name, 1, 1))) //just in case the variable name starts with a number
                return '$var' . substr($var_name, 1);
            else
                return $var_name;
        } else if ($block['l']) { // 'l' are hand coded fields in a block, we sanitatize it
            return $block['l']; //ToDo, how we sanitize this??
        } else { //sometimes $block is a script which in fact is an array of blocks
            $string = '';
            foreach ($block[0] as $key => $block_in_array) {
                $string .= ' ' . $this->blocksToPhp($block_in_array);
            }
            return $string;
        }
    }

    /*     * ***********************************************************************
     *  getBlockArguments: It may seem a bit commplicated the way that we fetch arguments,
     *  but the simpleXMLElement is quite annoying and it is not very logical how the arguments 
     *  get sorted depending if they are 'l', 'block', 'vars' etc. That's why we check ne by one
     * all the possible coombinations
     * ************************************************************************ */

    public function getBlockArguments($operator) {// The way I do this may seem very complicated but i am finding incredibly difficult to access the different elements in the $block on the right order. Even if it seems silly, this is the only way
        $operator_args = [];
        $parameters = [];
        foreach ($operator[0] as $key => $element) {
            array_push($operator_args, ['type_of_block' => $key, 'block' => $operator[0]->$key]);
        }
        if ($operator_args[0]['type_of_block'] == 'l' && $operator_args[1]['type_of_block'] == 'l') { // both aguments are 'l' (manually inserted by the user)
            $parameters[0] = $operator_args[0]['block'][0];
            $parameters[1] = $operator_args[1]['block'][1];
        } elseif ($operator_args[0]['type_of_block'] == 'block' && $operator_args[1]['type_of_block'] == 'l') { // First argument is a command block second manually introduced
            $parameters[0] = $this->blocksToPhp($operator_args[0]['block'][0]);
            $parameters[1] = $operator_args[1]['block'];
        } elseif ($operator_args[0]['type_of_block'] == 'l' && $operator_args[1]['type_of_block'] == 'block') { // First argument is 'l' (manually inntroduced) and second is a command block
            $parameters[0] = $operator_args[0]['block'];
            $parameters[1] = $this->blocksToPhp($operator_args[1]['block'][0]);
        } elseif (sizeof($operator->block) == 2) { // Both arfuments are command block
            $parameters[0] = $this->blocksToPhp($operator->block[0]);
            $parameters[1] = $this->blocksToPhp($operator->block[1]);
        }
        return $parameters;
    }

    public function getVariableIdFromBlock($variable) {
        /* For feeds $variable['var'] is something like "F9 - Feed description"
         * For attributes $variable['var'] is something like "A9 - Attribute description"
         * This functions return the id which in the examples is 9
         */
        $end = stripos($variable['var'], ' ');
        return substr($variable['var'], 1, $end - 1);
    }

    /*  End blocksToPhp()  */
    /* End of other methods  */

    /*     * ********************************* */
    /*  Get user feeds by node    */
    /*     * *************************** */

    public function get_user_feeds_by_node($userid) {
        global $feed_settings;
        include "Modules/feed/feed_model.php";

        $feed = new Feed($this->mysqli, $this->redis, $feed_settings);

        require "Modules/input/input_model.php";
        $input = new Input($this->mysqli, $this->redis, $feed);

        $array_of_feeds_by_node = array(); // The aim of this method is to fill up this array and treturn it
        $array_of_nodes = $input->get_inputs($userid); // returns inputs sorted by node
        $array_of_user_feeds = $feed->get_user_feeds($userid); // We use this one to get the name and tag of each feed

        foreach ($array_of_nodes as $nodeid => $array_of_inputs) {

            foreach ($array_of_inputs as $input) {
                $array_of_processes = explode(",", $input['processList']); //an example of the content of processList is: [processList] => 1:9,2:5,16:11 ---- In this example we can see the feedIDs are 9, 5 and 11
                if ($array_of_processes[0] != null) { //if the input has any processes aka feeds
                    if (!isset($array_of_feeds_by_node[$nodeid])) { // If this is the first time we find this node
                        $array_of_feeds_by_node[$nodeid] = array();
                        $array_of_feeds_by_node[$nodeid]['feeds'] = array();
                    }
                    foreach ($array_of_processes as $process) {
                        if ($process != null) {
                            $feedid = explode(":", $process)[1];
                            $name = $this->get_feed_name($array_of_user_feeds, $feedid);
                            $tag = $this->get_feed_tag($array_of_user_feeds, $feedid);
                            array_push($array_of_feeds_by_node[$nodeid]['feeds'], ['feedid' => $feedid, 'name' => $name, 'tag' => $tag]);
                        }
                    }
                }
            }
        }

        return $array_of_feeds_by_node;
    }

    private function get_feed_name($array_of_user_feeds, $feedid) {
        foreach ($array_of_user_feeds as $feed) {
            if ($feed['id'] == $feedid)
                return $feed['name'];
        }
    }

    private function get_feed_tag($array_of_user_feeds, $feedid) {
        foreach ($array_of_user_feeds as $feed) {
            if ($feed['id'] == $feedid)
                return $feed['tag'];
        }
    }

    /*  End get user feeds by node    */

    /*     * ************************************************************* */
    /* Get schedule (all the enabled rules)
      /*************************************************************** */

    public function get_schedule() {
        if ($this->redis) {
            return $this->redis_get_schedule();
        } else {
            return $this->mysql_get_schedule();
        }
    }

    public function redis_get_schedule() {
//ToDo
    }

    public function mysql_get_schedule() {
        $array_of_rules = array();
        $result = $this->mysqli->query("SELECT * FROM rules WHERE `enabled` = '1'");
        for ($i = 0; $row = (array) $result->fetch_object(); $i++) {
            $array_of_rules[$i] = $row;
        }
        return $array_of_rules;
    }

}
