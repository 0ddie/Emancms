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

}
