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



// Report all PHP errors 
ini_set('error_reporting', E_ALL);
// Set the display_errors directive to On 
ini_set('display_errors', 1);

$userid = 1;

// 0) Set working directory
$current_dir = __DIR__;
$new_dir = str_replace('/Modules/rules', '', $current_dir);
chdir($new_dir);

/*  1) A cron process must be set in order to start running this process. Once this 
 * process starts, it's kept in a while statement with a delay (sleep()). To 
 * avoid new cron processes overlap with this one a lockfile is set
 * 
 */
$fp = fopen("Modules/rules/lockfile", "w");
if (!flock($fp, LOCK_EX | LOCK_NB)) {
    echo "Already running\n";
    die;
}

// 2) Load settings and core scripts
require "process_settings.php";
require 'settings.php';

// 3) Database
$mysqli = new mysqli($server, $username, $password, $database);
//$redis = new Redis();
$redis = null;
//$redis->connect("127.0.0.1");
//
// 4) Include files
include "Modules/log/EmonLogger.php";

include "Modules/rules/rules_model.php";
$rules = new Rules($mysqli, $redis);

include "Modules/feed/feed_model.php";
$feed = new Feed($mysqli, $redis, $feed_settings);

include "Modules/register/register_model.php";
$register = new register($mysqli);


// 5) Run the "daemon", this is the "main" running in a loop
if(isset($rules_schedule_frequency))
    $rules_schedule_frequency = 1; //secs
while (true) {
    $rules->run_pendingAcks();
    $rules->run_enabledRules();
    sleep($rules_schedule_frequency); // Script update rate, defined in settings.php
}
?>
