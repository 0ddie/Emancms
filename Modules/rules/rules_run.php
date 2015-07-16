<?php

/*

  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org

 */

define('EMONCMS_EXEC', 1);

/* A cron process must be set in order to start running this process. Once this 
 * process starts, it's kept in a while statement with a delay (sleep()). To 
 * avoid new cron processes overlap with this one a lockfile is set
 * 
 * Uncomment when used as a cron
  $fp = fopen("lockfile", "w");
  if (!flock($fp, LOCK_EX | LOCK_NB)) {
  echo "Already running\n";
  die;
  }
 * */

// Report all PHP errors 
ini_set('error_reporting', E_ALL);

// Set the display_errors directive to On 
ini_set('display_errors', 1);

chdir("/var/www/OpenEMan"); // Ideally to be changed to something that autodetects the current directory

$userid = 1;

// 1) Load settings and core scripts
require "process_settings.php";
require 'settings.php';

// 2) Database
$mysqli = new mysqli($server, $username, $password, $database);
//$redis = new Redis();
$redis = null;
//$redis->connect("127.0.0.1");
// 3) Include files
include "Modules/rules/rules_model.php";
$rules = new Rules($mysqli, $redis);

include "Modules/log/EmonLogger.php";
$log = new EmonLogger();
$log->set_logfile(__DIR__ . '/rules.log'); // I think this may have problems when running on cgi

include "Modules/feed/feed_model.php";
$feed = new Feed($mysqli, $redis, $feed_settings);

include "Modules/register/register_model.php";
$register = new register($mysqli);

// 4) Run the "daemon", this is the "main" running in a loop
//  while (true) {
//run_acks_checker();
run_schedule();

// Script update rate
//sleep(3);
//}
// 5) Local functions

function run_schedule() {
    global $rules, $log;
    $time = time();

    $schedule = $rules->get_schedule(); // Fetches rules that are enabled

    foreach ($schedule as $rule) {
        //************************************************************************
        //**  Run the rule if it hasn't expired and we have passed the run_on_time
        //************************************************************************
        $expiry_time = strtotime($rule['expiry_date']); //when expiry_date === 0-0-0 0:0:0 then expiry_time = -62169987600. In this case the rule has not got expiry date
        if ($expiry_time > $time || $expiry_time == -62169987600) { //rule has not expired
            $run_on_time = strtotime($rule['run_on']);
            if ($run_on_time < $time) { // we have gone beyond the time to run -> we need to run the rule now
                $php_string_for_web = $rules->stagesToPhp($rule, 1); // We run the stage 1 of the rule
                //$php_string = str_replace('<br/>', PHP_EOL, $php_string_for_web);
                $php_string = strip_tags($php_string_for_web);
                print_r($php_string_for_web);
                $checkResult = exec('echo \'<?php ' . $php_string . '\' | php -l >/dev/null 2>&1; echo $?');
                if ($checkResult != 0) {
                    $log->warn("Error parsing rule code. Rule: " . $rule['ruleid'] . " - Stage: 1");
                    echo "Error parsing rule code. Rule: " . $rule['ruleid'] . " - Stage: 1";
                } else {
                    $result = eval($php_string);
                    echo "Evaling";
                    print_r($result);
                }
                $set_next_run_on = true;
                //echo "running<br>";
            } else {
                $set_next_run_on = false;
                echo "not running <br>";
            }
        }

        //************************************************************************
        //**  Disable the rule or set next time for the rule to be run 
        //************************************************************************
        if ($rule['frequency'] == 0) { // When frequency is "0" it means that the rule should only be run once
            $rules->disableRule($rule['ruleid']);
            $log->info("Rule disabled because frequency. Rule id: " . $rule['ruleid']);
            echo "rule disabled because frequency <br>";
        } elseif ($expiry_time < $time && $expiry_time != -62169987600) { // expirytime is '0' when expirydate is '0000-0-0 ...' in this case there is no expiry date then we don't disable the rule
            $rules->disableRule($rule['ruleid']);
            $log->info("Rule disabled because expiry date. Rule id: " . $rule['ruleid']);
            echo "rule disabled because expiry date <br>";
        } elseif ($set_next_run_on == true) {
            $new_run_on_time = $time + $rule['frequency'];
            $new_run_on_date = date("Y-m-d H:i:s", $new_run_on_time);
            $rules->setRunOn($rule['ruleid'], $new_run_on_date);
        }
    }
}
//Not sure if we need to remove the clisng tag when set up as cron
?>
