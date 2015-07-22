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
    private $log;

    public function __construct($mysqli, $redis) {
        $this->mysqli = $mysqli;
        $this->redis = $redis;
        $this->log = new EmonLogger();
        $this->log->set_logfile('Modules/rules/rules.log');
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
        $this->printForDeveloper("Fetching rules for user $userid");
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

    public function getRuleByRuleId($ruleid) {
        if ($this->redis) {
            return $this->redis_getRuleByRuleId($ruleid);
        } else {
            return $this->mysql_getRuleByRuleId($ruleid);
        }
    }

    public function redis_getRuleByRuleId($ruleid) {
//ToDo
    }

    public function mysql_getRuleByRuleId($ruleid) {
        $result = $this->mysqli->query("SELECT * FROM rules WHERE `ruleid`='$ruleid'");
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

    public function printForDeveloper($message) {
        global $rules_developer_mode;
        if ($rules_developer_mode === true) {
            echo '<pre>';
            print_r($message);
            echo '</pre>';
        }
    }

    public function logIfVerbose($message) {
        global $rules_log_mode;
        if ($rules_log_mode == 'verbose')
            $this->log->info($message);
    }

    /* End of other methods  */

    /* stagesToPhp()  */

    public function stagesToPhp($rule, $stage_to_run, $timedout) {
        global $rules_default_timeout;
        $blocks_string = $rule['blocks'];
        if (!isset($rules_default_timeout))
            $rules_default_timeout = 60; // secs, defined in settings.php

        ob_start();
// 1) Declare variables
        $variables_string = $this->getBlocksString($blocks_string, 'variables');
//$this->logIfVerbose($variables_string);
        $variables = new SimpleXMLElement($variables_string);
        echo "/* Variables */";
        echo '$timeout = ' . $rules_default_timeout . '; /* default value for timeout, if an ack has not been recieved in the timeout then $timedout will be true */';
        echo '$timedout = ' . (($timedout) ? 'true' : 'false') . ';';
        echo '$ruleid = ' . $rule['ruleid'] . ';';
        foreach ($variables->script as $var) {
//print_r($var->block);
//echo '$' . $var->block['var'] . ';';
            if ($this->blocksToPhp($var->block) != '$timedout')
                echo $this->blocksToPhp($var->block) . ';';
        }
        echo '';

// 2) Declare variables that hold feeds ids
        $feeds_ids_string = $this->getBlocksString($blocks_string, 'feeds');
        $feeds_ids = new SimpleXMLElement($feeds_ids_string);
        echo "/** Feed ids */";
        foreach ($feeds_ids->script as $id) {
//print_r($var->block);
            echo $this->blocksToPhp($id->block) . ' = (int) ' . $this->getVariableIdFromBlock($id->block) . ';';
        }
        echo '';

// 3) Declare variables that hold attributes ids
        $attributes_ids_string = $this->getBlocksString($blocks_string, 'attributes');
        $attributes_ids = new SimpleXMLElement($attributes_ids_string);
        echo "/** Attributes ids */";
        foreach ($attributes_ids->script as $id) {
//print_r($var->block);
            echo $this->blocksToPhp($id->block) . ' = (int) ' . $this->getVariableIdFromBlock($id->block) . ';';
        }
        echo '';

// 4) Switch for the Stages
        $stages_string = $this->getBlocksString($blocks_string, 'stages');
        $stages = new SimpleXMLElement($stages_string);
        echo '/** Run the current stage */';
        echo '$stage = ' . (int) $stage_to_run . ';';
        echo ('switch($stage){ ' );
        foreach ($stages->script as $stage) {
//print_r($stage);
            if ($stage->block[0]['s'] == 'Stage' && $stage->block[0]->l != '') { // Check this block starts with a "Stage" block and that the number of the stage is not empty
                echo ' case ' . (int) $stage->block[0]->l . ':';
                for ($index = 1; $stage->block[$index]; $index++) {
                    echo $this->blocksToPhp($stage->block[$index]) . ';';
                }
                echo '  break;';
            }
        }
        echo ' default:  $ruleid = $rule["ruleid"];  $this->log->warn("Stage not found in the script - Rule: ' . '$ruleid' . ' - Stage: $stage"); break;}';

        $php_code = ob_get_contents(); // $php_code uses  for breaking lines
        ob_clean();
        return $this->formatPhpCode($php_code); // $php_code is just one line of code, after formating the code it becomes readable with <pre></pre>
    }

    public function getBlocksString($blocks_string, $script_name) {
        $begining_of_slice = stripos($blocks_string, '<' . $script_name . '>');
        $end_of_slice = stripos($blocks_string, '</' . $script_name . '>') + strlen($script_name) + 3;
//echo '<pre>'.substr($blocks_string, $begining_of_slice, $end_of_slice - $begining_of_slice).'</pre>';
        return substr($blocks_string, $begining_of_slice, $end_of_slice - $begining_of_slice); // returns something like: <attributes><script x="10" y="35"><block var="1212120"/></script><script x="72" y="35"><block var="0x06500x06500x065100"/></script></attributes>
    }

    public function blocksToPhp($block) {
        if ($block['s']) { // if the block is command
            switch ($block['s']) {
                case 'doIf':
                    return '   if(' . $this->blocksToPhp($block->block) . '){   ' . $this->blocksToPhp($block->script) . '}';
                    break;
                case 'doIfElse':
//print_r($block->script);
                    $statement = '   if(' . $this->blocksToPhp($block->block) . '){     ' . $this->blocksToPhp($block->script[0]) . '   }';
                    $statement .= '   else{     ' . $this->blocksToPhp($block->script[1]) . '   }';
                    return $statement;
                    break;
                case 'requestFeed':
                    $feed_id = isset($block->l) ? (int) $block->l : $this->blocksToPhp($block->block);
                    /* echo '<pre>';
                      print_r($block);
                      echo '</pre>'; */
                    $statement = '/** $register->sendRequestToNode()*/';
                    $statement .= '$this->addPendingAck("requestFeed", $ruleid, $stage + 1, ["feedid"=>' . $feed_id . '], $timeout);';
                    return $statement;
                    break;
                case 'setAttribute':
                    $parameters = $this->getBlockArguments($block); // $parameters[0] is AttributeUid and $parameters[1] is the value
                    $statement = '/** $register->sendValueToNode() */';
                    $statement .= '$this->addPendingAck("setAttribute", $ruleid, $stage + 1, ["attributeUid"=>' . $parameters[0] . '], $timeout);';
                    return $statement;
                    break;
                case 'getLastFeed':
                    $feed_id = isset($block->l) ? (int) $block->l : $this->blocksToPhp($block->block);
                    $statement = '$feed->get(' . $feed_id . ')["value"]';
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
                    return ' $this->log->warn("Command block not recognized: ' . $block['s'] . '");';
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

    public function getSchedule() {
        if ($this->redis) {
            return $this->redis_getSchedule();
        } else {
            return $this->mysql_getSchedule();
        }
    }

    public function redis_getSchedule() {
//ToDo
    }

    public function mysql_getSchedule() {
        $array_of_rules = array();
        $result = $this->mysqli->query("SELECT * FROM rules WHERE `enabled` = '1'");
        for ($i = 0; $row = (array) $result->fetch_object(); $i++) {
            $array_of_rules[$i] = $row;
        }
        return $array_of_rules;
    }

    /*     * ************************************************************* */
    /* getPendingAcks()
      /*************************************************************** */

    public function getPendingAcks() {
        global $testing_rule;
        if ($this->redis) {
            return $this->redis_getPendingAcks($testing_rule);
        } else {
            return $this->mysql_getPendingAcks($testing_rule);
        }
    }

    public function redis_getPendingAcks($testing_rule) {
//ToDo
    }

    public function mysql_getPendingAcks($testing_rule) {
        global $session;
        $array_of_rules = array();
        if ($testing_rule === true) {
            $table = 'rules_pending_acks_testing_rule';
            $query = "SELECT * FROM $table WHERE userid = '" . $session['userid'] . "'";
        } else {
            $table = 'rules_pending_acks';
            $query = "SELECT * FROM $table";
        }
        $result = $this->mysqli->query($query);
        for ($i = 0; $row = (array) $result->fetch_object(); $i++) {
            $array_of_rules[$i] = $row;
        }
        return $array_of_rules;
    }

    /*     * *****************************************
     *  run_pendingAcks()
     *  run_schedule()
     */

    public function run_pendingAcks() {
        global $rules_log_mode;
        $time = time();
        $array_of_pending_acks = $this->getPendingAcks(); // Fetches all the pending acks
        $this->printForDeveloper("Fetching pending acks");
        if (sizeof($array_of_pending_acks) === 0)
            $this->printForDeveloper('There are no pending acks');

        foreach ($array_of_pending_acks as $pending_ack) {
            $this->printForDeveloper("<b>Checking pending ack from rule: " . $pending_ack['ruleid'] . "</b> - Type: " . $pending_ack['type'] . "  -  Args: " . $pending_ack['args']);
            $ack_received = $this->checkAck($pending_ack);
            switch ($ack_received['success']) {
                case 0: // Ack not received, we check timeout
                    $time_left_for_timeout = strtotime($pending_ack['request_time']) + $pending_ack['timeout'] - time();
                    $this->printForDeveloper("Ack not received - Time left before timing out: $time_left_for_timeout");
                    $request_time = strtotime($pending_ack['request_time']);
                    if (time() > $request_time + $pending_ack['timeout']) { // Timeout passed
                        $this->printForDeveloper('Pending ack timedout');
                        $this->deleteAllPendingAcks($pending_ack['ruleid']);
                        $this->runRule($this->getRuleByRuleId($pending_ack['ruleid']), $pending_ack['next_stage'], $timedout = true);
                        break 2; // We exit from the switch and the foreach
                    }
                    break;
                case 1: // Ack received .We delete this pending ack and if there are not more pending acks for this rule then we run the next stage of the rule
                    $this->printForDeveloper("Ack received");
                    $this->deletePendingAck($pending_ack['id']);
                    if (!$this->morePendingAcksForRule($pending_ack['ruleid'])) {
                        $this->printForDeveloper("There aren't anymore pending acks for this rule to wait for.");
                        $this->runRule($pending_ack['ruleid'], $pending_ack['next_stage']);
                    } else
                        $this->printForDeveloper("There are more pending acks for this rule. Wait for other acks to arrive before running next stage");
                    break;
                default: // There has been an error
                    $this->log->warn("Rule: " . $pending_ack['ruleid'] . ": error checking ack. Message: " . $ack_received['message'] . "\n");
                    $this->printForDeveloper("Rule: " . $pending_ack['ruleid'] . ": error checking ack. Message: " . $ack_received['message']);
                    break;
            }
        }
    }

    public function run_enabledRules() { // Only runs the enabled rules which "run_on" time has been reached
        global $rules, $rules_developer_mode, $rules_log_mode;
        $time = time();
        $this->printForDeveloper('Fetching enabled rules');
        $schedule = $this->getSchedule(); // Fetches rules that are enabled
        $set_next_run_on = false;

        foreach ($schedule as $rule) {
//************************************************************************
//**  Run the rule if there are not any pending ack for it, if there are it means we haven't finished running it since last time
//**  Also run the rule if it hasn't expired and we have passed the run_on_time
//************************************************************************
            $expiry_time = strtotime($rule['expiry_date']); //when expiry_date === 0-0-0 0:0:0 then expiry_time = -62169987600. In this case the rule has not got expiry date
            $this->printForDeveloper("<b>Rule: " . $rule['ruleid'] . "</b>  -  Name: " . $rule['name'] . "  -  Description: " . $rule['description']);
            $this->printForDeveloper("Checking if there are pending acks");
            if ($rules->morePendingAcksForRule($rule['ruleid']) === false) { // There are not pending acks for this rule
                $this->printForDeveloper('No pending acks. Run rule now');
                if ($expiry_time > $time || $expiry_time == -62169987600) { //rule has not expired
                    $run_on_time = strtotime($rule['run_on']);
                    if ($run_on_time > $time) { // We haven't reached the time to run -> we don't run the rule now
                        $set_next_run_on = false;
                        $this->printForDeveloper("Rule with ruleid: " . $rule['ruleid'] . ", run_on time no reached");
                    } else { // Run the rule
                        $this->runRule($rule, 1);
                        $set_next_run_on = true;
                    }
                }
            } else
                $this->printForDeveloper("There are pending acks for this rule. Rule will not be run.");

//************************************************************************
//**  Disable the rule or set next time for the rule to be run 
//************************************************************************
            if ($rule['frequency'] == 0) { // When frequency is "0" it means that the rule should only be run once
                $this->disableRule($rule['ruleid']);
                $this->logIfVerbose("Rule disabled because frequency. Rule id: " . $rule['ruleid']);
                $this->printForDeveloper("Rule disabled because frequency. Ruleid: " . $rule['ruleid']);
            } elseif ($expiry_time < $time && $expiry_time != -62169987600) { // expirytime is '0' when expirydate is '0000-0-0 ...' in this case there is no expiry date then we don't disable the rule
                $this->disableRule($rule['ruleid']);
                $this->logIfVerbose("Rule disabled because expiry date. Rule id: " . $rule['ruleid']);
                $this->printForDeveloper("Rule disabled because expiry date. Rule id: " . $rule['ruleid']);
            } elseif ($set_next_run_on == true) {
                $new_run_on_time = $time + $rule['frequency'];
                $new_run_on_date = date("Y-m-d H:i:s", $new_run_on_time);
                $this->setRunOn($rule['ruleid'], $new_run_on_date);
            }
        }
    }

    /*     * ***************************************************
     *    checkAck() returns an array ['success'=>  integer, 'message'=> string, 'args'=>[]]
     *    The value of succes:
     *      - 0: ack not received
     *      - 1: ack received
     *      - Any other integer: for errors  checking el ack
     * **************************************************** */

    public function checkAck($pending_ack) {
        $args = json_decode($pending_ack['args'], true);
        $ack_received = false;

        switch ($pending_ack['type']) {
            case 'requestFeed':
                global $feed;
                /* We check when was the feed last update and if updated then ack received   */
                $last_feed = $feed->get($args['feedid']);
//$last_feed = 0;
                if (!$last_feed['id']) // If the feed doesn't exist
                    return ['success' => 2, 'message' => ('Feed does not exist, the given feedid is ' . $args['feedid'])];
                else {
                    if ($last_feed['time'] > strtotime($pending_ack['request_time']))
                        return ['success' => 1, 'message' => 'Ack received'];
                    else
                        return ['success' => 0, 'message' => 'Ack not received'];
                }
                break;
            case 'setAttribute':
// ToDo when the register->setup works properly
                break;
        }
    }

    public function runRule($rule, $stage, $timedout = false) {
        global $feed, $register;

        $php_string = $this->stagesToPhp($rule, $stage, $timedout); // We run the stage 1 of the rule;
        $this->printForDeveloper("Running rule with ruleid: " . $rule['ruleid'] . ' - Stage: ' . $stage . ($timedout === true ? ' - Timedout: true' : ''));
//$this->printForDeveloper($php_string);
        $syntax_error = $this->checkCodeSyntax($php_string);
        if ($syntax_error !== 'No syntax errors detected in -') {
            $this->log->warn("Rule: " . $rule['ruleid'] . " - There is an error with the syntax of the rule - The error message: $syntax_error");
            $this->printForDeveloper("Rule: " . $rule['ruleid'] . " - There is an error with the syntax of the rule - The error message: $syntax_error");
        }
// eval the code. We run the code even if there has been syntax error, running eval and catching the output will let us display more errrors
        $this->printForDeveloper("eval()");
        error_reporting(-1);
        ob_start(); // to get errors thrown by eval()
        eval($php_string);
        $eval_output = ob_get_clean();
        if ($eval_output != '') {
            $this->log->warn("Rule: " . $rule['ruleid'] . " - Stage: 1 --- eval() output: \n" . strip_tags($eval_output) . "\n");
            $this->printForDeveloper("Rule: " . $rule['ruleid'] . " - Stage: 1 --- eval() output: \n" . strip_tags($eval_output));
        }
    }

    public function addPendingAck($type, $ruleid, $next_stage, $args, $timeout) {
        global $session, $testing_rule;

        $date = date("Y-m-d H:i:s", time());
        $error = '';
        if ($testing_rule === true) {
            $table = 'rules_pending_acks_testing_rule';
            $query = "INSERT INTO `$table` (`request_time`, `type`, `ruleid`, `next_stage`, `args`, `timeout`, `userid`) VALUES ('$date','$type', '$ruleid', '$next_stage', '" . json_encode($args) . "', '$timeout', '" . $session['userid'] . "')";
        } else {
            $table = 'rules_pending_acks';
            $query = "INSERT INTO `$table` (`request_time`, `type`, `ruleid`, `next_stage`, `args`, `timeout`) VALUES ('$date','$type', '$ruleid', '$next_stage', '" . json_encode($args) . "', '$timeout')";
        }

        if ($this->redis) {
//ToDo
        } else {
            $result = $this->mysqli->query($query);
            if ($result != true) {
                $error = $this->mysqli->error;
            } else
                $this->printForDeveloper("\nPending ack added to database. "
                        . "\n   Type: $type \n   Next stage: $next_stage \n   Request time: $date \n   Timeout: $timeout \n   Args: " . json_encode($args) . "\n");
        }
        if ($error != '') {
            $this->printForDeveloper("Pending ack not added to database - Rule: $ruleid - Stage: $stage - Error: $error");
            $this->printForDeveloper($query);
            $this->log->warn("Pending ack not added to database - Rule: $ruleid - Stage: $stage - Error: $error");
            $this->log->warn($query);
        }
        return $result;
    }

    public function deleteAllPendingAcks($ruleid) {
        global $testing_rule;

        if ($testing_rule === true) {
            $query = "DELETE FROM rules_pending_acks_testing_rule WHERE `ruleid` = '$ruleid'";
        } else {
            $query = "DELETE FROM rules_pending_acks WHERE `ruleid` = '$ruleid'";
        }
        $pending_acks_deleted = $this->mysqli->query($query);
        if ($pending_acks_deleted && $this->redis) {
// Delete from redis;
        }
        if ($pending_acks_deleted === false) {
            $this->printForDeveloper("Pending acks for rule $ruleid could not be deleted deleted. Error: " . $this->mysqli->error);
            $this->logIfVerbose("Pending acks for rule $ruleid could not be deleted deleted. Error: " . $this->mysqli->error);
        } else
            $this->printForDeveloper("All pending acks deleted for rule with ruleid $ruleid");
        return $pending_acks_deleted;
    }

    public function deletePendingAck($id) {
        global $testing_rule;
        if ($testing_rule === true) {
            $query = "DELETE FROM rules_pending_acks_testing_rule WHERE `id` = '$id'";
        } else {
            $query = "DELETE FROM rules_pending_acks WHERE `id` = '$id'";
        }
        $pending_ack_deleted = $this->mysqli->query($query);
        if ($pending_ack_deleted && $this->redis) {
// Delete from redis;
        }
        if ($pending_ack_deleted === false) {
            $this->printForDeveloper("Pending ack (id = $id) could not be deleted. Error: " . $this->mysqli->error);
            $this->logIfVerbose("Pending ack (id = $id) could not be deleted. Error: " . $this->mysqli->error);
        } else
            $this->printForDeveloper('Pending ack deleted');
        return $pending_ack_deleted;
    }

    public function morePendingAcksForRule($ruleid) {
        global $testing_rule;
        if ($testing_rule === true)
            $query = "SELECT * FROM rules_pending_acks_testing_rule WHERE `ruleid` = '$ruleid'";
        else
            $query = "SELECT * FROM rules_pending_acks WHERE `ruleid` = '$ruleid'";
        if ($this->redis) {
// ToDo
        } else {
            $result = $this->mysqli->query($query);
        }
        if ($result->num_rows > 0)
            return true;
        else
            return false;
    }

    /*     * *************************
     *  formatPhpCode($code_string) is not working
     */

    public function formatPhpCode($code_string) {
        require_once ('Modules/rules/libraries/phpbeautifier/Beautifier.php');
        require_once ('Modules/rules/libraries/phpbeautifier/Beautifier/Batch.php');

        $oBeaut = new PHP_Beautifier();
        $oBatch = new PHP_Beautifier_Batch($oBeaut);
        $oBeaut->addFilter('ArrayNested');
        $oBeaut->addFilter('NewLines', array('before' => 'T_DOC_COMMENT', 'after' => ''));
        $oBeaut->setInputString('<?php ' . $code_string . ' ?>'); // php tags are needed for the beautifier to work but we remove them later
        $oBeaut->process();
        $formatted_code = $oBeaut->get();
        $formatted_code = str_replace(['<?php', '?>'], '', $formatted_code);
        return $formatted_code;
    }

    public function checkCodeSyntax($php_code) {
//$result = exec('echo \'<?php ' . $php_code . '\' | php -l >/dev/null 2>&1; echo $?', $out, $ret);
        $result = exec("echo '<?php $php_code' | php -l 2>/dev/null", $out, $ret);
//var_dump($out);
//var_dump($ret);
        if ($result != 0) // If there has been error
            $result = $out;
        return $result;
    }

    public function EvalCode($php_code) {
        return eval($php_code);
    }

}
