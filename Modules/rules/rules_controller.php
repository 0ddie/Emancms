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

function rules_controller() {
    global $mysqli, $redis, $session, $route;

    include "Modules/rules/rules_model.php";
    $rules = new Rules($mysqli, $redis);

    /*     * ************************************************** */
    /* Do we need to check if the this is the first 
     * time we run the controller after the server swtches
     *  on. I guess if this is the case we would have to 
     * copy into redis all the tables from mysql 
     * 
     *              ToDO     */
    /*     * ************************************************** */
    if ($rules->first_run())
        $rules->load_tables_into_redis();

    switch ($route->action) {
        case 'list':
            if ($session['read'] == 1) {
                $list_of_rules = $rules->get_rules($session['userid']);
                switch ($route->format) {
                    case 'html':
                        //echo json_encode($list_of_rules);
                        $result = view("Modules/rules/Views/rules_list.php", ['list_of_rules' => $list_of_rules]);
                        break;
                    case 'json':
                        $result = "";
                        break;
                }
            } else {
                switch ($route->format) {
                    case 'html':
                        $result = "<h2>Authentication failed</h2>"
                                . "<p>You are not allowed to see this content. Please login first</p>";
                        break;
                    case 'json':
                        $result = "Error: ERROR-CODE - Authentication failed";
                        break;
                }
            }

            break;
        case 'create':
            break;
        case 'api':
            $result = view("Modules/rules/Views/rules_api.php", array());
            break;
        case 'edit':
            break;
        case 'delete':
            break;
    }

    //return array('content'=>$result);
    return array('content' => $result);
}
