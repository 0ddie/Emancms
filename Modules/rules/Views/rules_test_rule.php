<?php
global $path, $mysqli, $redis;

include_once "Modules/rules/rules_model.php";
$rules = new Rules($mysqli, $redis);

$formatted_code = $rules->formatPhpCode(strip_tags($rules->stagesToPhp($args['rule'], 1, false)));
?>
<!--<script type="text/javascript" src="<?php //echo $path;                                      ?>Lib/angularjs/angular.min.js"></script>-->
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

<link href="<?php echo $path; ?>Modules/rules/Views/rules.css" rel="stylesheet">

<base href="<?php echo $path; ?>">
<br>
<div id="apihelphead">
    <div style="float:right;">

        <a href="<?php echo $path; ?>rules/edit?ruleid=<?php echo $args['rule']['ruleid'] ?>"><?php echo _('Back to edit rule') ?></a>
        <a href="rules/list" ><?php echo _('Close'); ?></a>
    </div>
</div>

<div ng-app="moduleViewApp" ng-controller="moduleViewAppCtrl">
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
                <p><?php echo str_replace(['<?php','?>'], '', $formatted_code) ?></p>
            </div>
        </div>
    </div>






