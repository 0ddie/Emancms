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
                        $result = view("Modules/rules/Views/rules_list.php", ['list_of_rules' => $list_of_rules]);
                        break;
                    case 'json':
                        $result = json_encode($list_of_rules);
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
        case 'add':
            $result = view("Modules/rules/Views/rules_edit_rule.php", ['mode' => 'add']);
            break;
        case 'edit':
            $rule = $rules->get_rule((int) get('ruleid'), $session['userid']);
            $result = view("Modules/rules/Views/rules_edit_rule.php", ['mode' => 'edit', 'rule' => $rule]);
            break;
        case 'save-rule':
            $attributes = ['ruleid' => get('ruleid'),
                'name' => get('name'),
                'description' => get('description'),
                'run_on' => get('run_on'),
                'expiry_date' => get('expiry_date'),
                'frequency' => get('frequency'),
                'blocks' => get('blocks'),
                'enabled' => get('enabled'),
                'mode' => get('mode')];
            $rule_id = $rules->save_rule($attributes); //returns the id of the rule or 0 if something went wrong
            $rule_saved = $rule_id == 0 ? false : true;
            switch ($attributes['mode']) {
                case 'save': // Save and close
                $list_of_rules = $rules->get_rules($session['userid']);
                $result = view("Modules/rules/Views/rules_list.php", ['list_of_rules' => $list_of_rules, 'rule_saved' => $rule_saved]);
                    break;
                case 'apply': // Save and stay editting the rule
                    $rule = $rules->get_rule($rule_id, $session['userid']);
                    $result = view("Modules/rules/Views/rules_edit_rule.php", ['mode' => 'edit', 'rule' => $rule, 'rule_saved' => $rule_saved]);
                    break;
                case 'apply_and_test':
                    $rule = $rules->get_rule($rule_id, $session['userid']);
                    $result = view("Modules/rules/Views/rules_test_rule.php", ['rule' => $rule, 'rule_saved' => $rule_saved]);
                    break;
            }
            break;
        case 'api':
            $result = view("Modules/rules/Views/rules_api.php", array());
            break;
        case 'add-mock-rules':
            $rules->add_mock_rules();
            $list_of_rules = $rules->get_rules($session['userid']);
            $result = view("Modules/rules/Views/rules_list.php", ['list_of_rules' => $list_of_rules]);
            break;
        case 'delete':
            if ($session['write'] == 1) {
                $rule_deleted = $rules->delete_rule((int) (get('ruleid')), $session['userid']);
                $list_of_rules = $rules->get_rules($session['userid']);
                switch ($route->format) {
                    case 'html':
                        $result = view("Modules/rules/Views/rules_list.php", ['list_of_rules' => $list_of_rules, 'rule_deleted' => $rule_deleted]);
                        break;
                    case 'json':
                        if ($rule_deleted == true)
                            $result = 'success';
                        else
                            $result = 'ERROR - Rule not deleted';
                        break;
                }
            } else {
                switch ($route->format) {
                    case 'html':
                        $result = "<h2>Permission denied</h2>"
                                . "<p>You are not allowed to delete rules</p>";
                        break;
                    case 'json':
                        $result = "Error: ERROR-CODE - permission denied";
                        break;
                }
            }
            break;
    }

    //return array('content'=>$result);
    return array('content' => $result);
}

/*
 *     http://localhost/OpenEMan/rules/delete.html?ruleid=3
 * http://localhost/OpenEMan/rules/save-rule?name=%22name%22&description=%22descr%22&run_on=%22run&expiry_date=%22expiry%22&frequency=%22%22&blocks=%22nsls%22
 *  * */

