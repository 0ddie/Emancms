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
        $result = $this->mysqli->query("SELECT ruleid,name,description,run_on,expiry_date,frequency FROM rules WHERE `userid` = '$userid'");
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
        $result = $this->mysqli->query("SELECT ruleid,name,description,run_on,expiry_date,frequency,userid,blocks FROM rules WHERE `userid` = '$userid' AND `ruleid`='$ruleid'");
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
        $expiry_date = preg_replace('/([^0-9\-])/', '', $attributes['expiry_date']);
        $frequency = (int) $attributes['frequency'];
        $blocks = preg_replace('/[^\w\s-.]/', '', $attributes['blocks']);
        //print_r("hola");
        //print_r($attributes['ruleid']);
        //echo 'hola' + $this->rule_exists($attributes['ruleid']);
        if ($this->rule_exists($attributes['ruleid']) == false) {
            $rule_saved = $this->mysqli->query("INSERT INTO `rules` (`userid`, `name`, `description`, `run_on`, `expiry_date`, `frequency`, `blocks`) VALUES ('$userid', '$name', '$description', '$run_on', '$expiry_date', '$frequency', '$blocks')");
            if ($this->redis && $rule_saved) {
                //ToDo insert rule
            }
            if ($rule_saved == false || $rule_saved == 0)
                return 0;
            else
                return $this->mysqli->insert_id;
        } else {
            $rule_saved = $this->mysqli->query("UPDATE `rules` SET `name`='$name', `description`='$description', `run_on`='$run_on', `expiry_date`='$expiry_date', `frequency`='$frequency', `blocks`='$blocks' WHERE `ruleid`= '$ruleid' AND `userid` = '$userid'");
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
        //print_r($query_result);
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
        /* echo "<pre>";
          print_r($array_of_feeds_by_node);
          echo "</pre>"; */
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
}
