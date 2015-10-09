<?php
global $path, $mysqli, $redis, $rules_developer_mode, $log;

include_once "Modules/rules/rules_model.php";
$rules = new Rules($mysqli, $redis);

global $feed_settings;
include_once "Modules/feed/feed_model.php";
global $feed;
$feed = new Feed($mysqli, $redis, $feed_settings);

/** Variables used in the view  */
$php_code = $rules->stagesToPhp($args['rule'], 1, $timedout = false);
$syntax_error = $rules->checkCodeSyntax($php_code);
global $testing_rule; // Used as global to tell the methods (mainly the ones that deal with pending acks) we are testing. 
$testing_rule = true;

$rules_developer_mode = true; // Force developer mode to allow print
?>
<!--<script type="text/javascript" src="<?php //echo $path;                                                         ?>Lib/angularjs/angular.min.js"></script>-->
<script type="text/javascript" src="<?php echo $path; ?>Lib/angularjs/angular.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/angularjs/ui-bootstrap-tpls-0.13.0.min.js"></script>



<script>
    var moduleViewApp = angular.module('moduleViewApp', ['ui.bootstrap']);

    /*moduleViewApp.config(['$locationProvider', function ($locationProvider) {
     $locationProvider.html5Mode(true);
     }]);*/

    moduleViewApp.controller('moduleViewAppCtrl', function ($scope, $modal, $location, $window) {

        /*  Objects in the scope  */
        $scope.rule_saved = <?php echo isset($args['rule_saved']) ? json_encode($args['rule_saved']) : "null" ?>;
        /*  End Objects in the scope  */

        /*  Functions in the scope  */

        /*  End Functions in the scope  */

    })
</script>
<div class="container">
<link href="<?php echo $path; ?>Modules/rules/Views/rules.css" rel="stylesheet">

<base href="<?php echo $path; ?>">
<div ng-app="moduleViewApp" ng-controller="moduleViewAppCtrl"  id="test-rule">
    <div id="apihelphead">
        <div style="float:right;">

            <a href="<?php echo $path; ?>rules/edit?ruleid=<?php echo $args['rule']['ruleid'] ?>"><?php echo _('Back to edit rule') ?></a>
            <a href="rules/list" ><?php echo _('Close'); ?></a>
        </div>
    </div>

    <div class="container test_rule">
        <div id="localheading"><h2><?php echo _('Rule Test'); ?></h2></div>
        <div id="rule_saved" ng-if="rule_saved !== null"><p class="bg-success"><?php echo _('Rule saved: ') ?> {{rule_saved}}</p></div>
        <div class="container">
            <table class="table">
                <tr><td><?php echo _('Name') ?>: </td><td><?php echo $args['rule']['name'] ?></td></tr>
                <tr><td><?php echo _('Description') ?>: </td><td><?php echo $args['rule']['description'] ?></td></tr>
                <tr><td><?php echo _('Run on') ?>: </td><td><?php echo $args['rule']['run_on'] ?></td></tr>
                <tr><td><?php echo _('Expiry date') ?>: </td><td><?php echo $args['rule']['expiry_date'] ?></td></tr>
                <tr><td><?php echo _('Frequency') ?>: </td><td><?php echo $args['rule']['frequency'] ?></td></tr>
            </table>
            <h3><?php echo _('Php code') ?></h3>
            <p><?php echo _('Below is the php code generated for your rule') ?></p>
            <div id="code">
                <p><?php echo '<pre>' . $php_code . '</pre>' ?></p>
            </div>
            <h3><?php echo _('Sintax check') ?></h3>
            <p><?php echo _('This is the message we get when we run "php -l" with the generated code') ?></p>
            <pre><?php echo $syntax_error ?></pre>
            <?php
            if ($syntax_error !== 'No syntax errors detected in -') {
                echo '<p>' . _('Because there are syntax errors, we have evaluated the code and this is the output:') . '</p>';
                error_reporting(-1);
                ob_start(); // to get errors thrown by eval()
                $rules->EvalCode($php_code);
                $error = ob_get_contents();
                ob_end_clean();
                //echo $error;
                $rules->printForDeveloper(strip_tags($error));
            } else {
                ?>
                <h3><?php echo _('Console') ?></h3>
                <p><?php echo _('The rule is running now.') ?></p>
                <div id="rule_test_console">
                    <?php
                    //$rules->runRule($args['rule'], 1, false);
                    $rules->runRule($args['rule'], 1, false);
                    $rules->run_pendingAcks();
                    /* We need to do the for below as ajax to update the console with every check of the pending acks
                     * for ($i=0;$i < 60;$i++) { // replace timeout for the one in settings.php
                      $rules->run_pendingAcks();
                      sleep(1);
                      } */
                    ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
</div>





