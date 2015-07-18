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

$userid = 1;

// 0) Set working directory
$current_dir = __DIR__;
$new_dir = str_replace('/Modules/rules', '', $current_dir);
chdir($new_dir);

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

print_r($feed->get(1));

// 4) Run the "daemon", this is the "main" running in a loop
//while (true) {
//  $rules->run_pendingAcks();
$rules->run_schedule();
//  sleep(3); // Script update rate
//}
?>
