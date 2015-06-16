<?php
global $path;
?>
<!--<script type="text/javascript" src="<?php //echo $path;                               ?>Lib/angularjs/angular.min.js"></script>-->
<script type="text/javascript" src="<?php echo $path; ?>Lib/angularjs/angular.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/angularjs/ui-bootstrap-tpls-0.13.0.min.js"></script>



<script>
    var moduleViewApp = angular.module('moduleViewApp', ['ui.bootstrap']);

    /*moduleViewApp.config(['$locationProvider', function ($locationProvider) {
            $locationProvider.html5Mode(true);
        }]);*/

    moduleViewApp.controller('moduleViewAppCtrl', function ($scope, $modal, $location, $window) {

        /*  Objects in the scope  */
        $scope.rules = <?php echo json_encode($args['list_of_rules']) ?>;
        $scope.rule_deleted = <?php echo isset($args['rule_deleted']) ? json_encode($args['rule_deleted']) : "null" ?>;
        $scope.rule_saved = <?php echo isset($args['rule_saved']) ? json_encode($args['rule_saved']) : "null" ?>;
        /*  End Objects in the scope  */

        /*  Functions in the scope  */
        $scope.openModal = function (rule_to_delete) {
            var modalInstance = $modal.open({
                template: '<div class = "modal-header"><h3 class="modal-title"><?php echo _('Delete rule'); ?></h3></div ><div class="modal-body"><p><?php echo _('You  are going to delete the rule: ') ?><b> {{rule_to_delete.name}}</b></p> <p><?php echo _('If you delete a rule you will lose it forever, be careful!!'); ?> </p></div><div class="modal-footer"> <button class="btn btn-primary" ng-click="ok()"> <?php echo _('OK') ?> </button><button class="btn btn-warning" ng-click="cancel();"> <?php echo _('Cancel') ?> </button ></div>',
                controller: function ($scope, $modalInstance) {
                    $scope.rule_to_delete = rule_to_delete;
                    $scope.ok = function () {
                        $modalInstance.close();
                        //$location.path('rules/delete.json?ruleid=' + $scope.rule_to_delete.ruleid);
                        //$location.replace();
                        $window.location.href = "<?php echo $path; ?>rules/delete.html?ruleid=" + $scope.rule_to_delete.ruleid;
                    };
                    $scope.cancel = function () {
                        $modalInstance.dismiss();
                    };
                }
            });

        };
        /*  End Functions in the scope  */

    })
</script>

<link href="<?php echo $path; ?>Modules/rules/Views/rules.css" rel="stylesheet">

<base href="<?php echo $path; ?>">
<br>
<div id="apihelphead">
    <div style="float:right;">
        <a title="<?php echo _('Add rule')?>" href="rules/add"><i class="icon-plus-sign"></i></a>
        <a href="rules/add-mock-rules" ><?php echo _('Add mock rules'); ?></a>
        <a href="rules/api"><?php echo _('Rules API Help'); ?></a>
    </div>
</div>

<div ng-app="moduleViewApp" ng-controller="moduleViewAppCtrl">
    <div class="container">
        <div id="localheading"><h2><?php echo _('Rules'); ?></h2></div>
        <div id="rule_deleted" ng-if="rule_deleted !== null"><p class="bg-success"><?php echo _('Rule deleted: ')?> {{rule_deleted}}</p></div>
        <div id="rule_saved" ng-if="rule_saved !== null"><p class="bg-success"><?php echo _('Rule saved: ')?> {{rule_saved}}</p></div>
        <div id="list_of_rules">
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <th><a><?php echo _('Rule Id'); ?></a></th>
                        <th><a><?php echo _('Name'); ?></a></th>
                        <th><a><?php echo _('Description'); ?></a></th>
                        <th><a><?php echo _('Run on'); ?></a></th>
                        <th><a><?php echo _('Expiry date<br />(0 for no expiry date)'); ?></a></th>
                        <th><a><?php echo _('Frequency<br />(secs)'); ?></a></th>
                        <th><a><?php echo _('Enabled'); ?></a></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tbody>
                <tbody>
                    <tr ng-repeat="rule in rules">
                        <td>{{rule.ruleid}}</td>
                        <td>{{rule.name}}</td>
                        <td>{{rule.description}}</td>
                        <td>{{rule.run_on}}</td>
                        <td>{{rule.expiry_date}}</td>
                        <td>{{rule.frequency}}</td>
                        <td>{{rule.enabled === '0' ? <?php echo _('false') ?> : <?php echo _('true') ?>}}</td>
                        <td><a title="<?php echo _('Edit rule')?>" href="<?php echo $path; ?>rules/edit?ruleid={{rule.ruleid}}"><i class='icon-pencil'></i></a></td>
                        <td title="<?php echo _('Delete rule')?>" class='delete-dialog-opener'><i class='icon-trash' ng-click="openModal(rule)"></i></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>






