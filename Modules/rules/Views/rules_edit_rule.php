<?php
global $path;

// we set the header text according to the mode
if ($args['mode'] == 'add')
    $header = 'New Rule';
else
    $header = 'Edit Rule';

//arrays containing the attributes and feeds (sorted by node) to be used in the visual programmer
global $mysqli, $redis, $session, $route;

//include "Modules/rules/rules_model.php";
$rules = new Rules($mysqli, $redis);
$array_of_feeds_by_node = $rules->get_user_feeds_by_node($session['userid']); // array like: Array ( [0] => Array ( [id] => 6 [name] => Power [userid] => 1 [tag] => [time] => 1430748375 [value] => 100 [datatype] => 1 [public] => 0 [size] => [engine] => 5 ) [1] => Array ( [id] => 7 [name] => Poadasdawer [userid] => 1 [tag] => [time] => [value] => [datatype] => 1 [public] => 0 [size] => [engine] => 5 ) [2] => Array ( [id] => 8 [name] => Poaeeedasdawer [userid] => 1 [tag] => [time] => [value] => [datatype] => 1 [public] => 0 [size] => [engine] => 5 ));
?>
<!--<script type="text/javascript" src="<?php //echo $path;                                                      ?>Lib/angularjs/angular.min.js"></script>-->
<script type="text/javascript" src="<?php echo $path; ?>Lib/angularjs/angular.js"></script>

<!-- Visual programmer  -->
<script type="text/javascript" src="<?php echo $path; ?>Modules/rules/scripts/morphic.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/rules/scripts/emonCMS_RulesIDE_gui.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/rules/scripts/widgets.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/rules/scripts/objects.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/rules/scripts/emonCMS_RulesObjects.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/rules/scripts/blocks.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/rules/scripts/emonCMS_RulesBlocks.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/rules/scripts/threads.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/rules/scripts/byob.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/rules/scripts/emonCMS_RulesByob.js"></script>

<script type="text/javascript">
    var world;
    window.onload = function () {
        // Create World
        world = new WorldMorph(document.getElementById('world'));
        world.worldCanvas.focus();
        world.isDevMode = true;
        world.setWidth(1170);
        //world.setHeight(250);

        // Create IDE and add it to the world - Everything related to the IDE is in "emonCMS_RulesIDE_gui.js" 
        var array_of_feeds_by_node = <?php echo json_encode($array_of_feeds_by_node) ?>;
        console.log(array_of_feeds_by_node);
        rulesIDE = new emonCMS_RulesIDE_Morph(world.width(), world.height(),array_of_feeds_by_node);
        world.add(rulesIDE);
        
        

        setInterval(loop, 1);
    };
    function loop() {
        world.doOneCycle();
    }
</script>



<script>
    var moduleViewApp = angular.module('moduleViewApp', []);

    moduleViewApp.controller('moduleViewAppCtrl', function ($scope) {

        /*  Objects in the scope  */
<?php
if (isset($args['rule'])) {
    $rule = $args['rule'];
    ?>
            $scope.rule_attibutes = {'ruleid': '<?php echo $rule['ruleid'] ?>',
                'name': '<?php echo $rule['name'] ?>',
                'description': '<?php echo $rule['description'] ?>',
                'run_on': '<?php echo $rule['run_on'] ?>',
                'expiry_date': '<?php echo $rule['expiry_date'] ?>',
                'frequency': <?php echo $rule['frequency'] ?>,
                'blocks': '<?php echo $rule['blocks'] ?>'
            };
            $scope.rule_saved = <?php echo isset($args['rule_saved']) ? json_encode($args['rule_saved']) : "null" ?>;

<?php } else {
    ?>
            $scope.rule_attibutes = {'ruleid': '', 'name': '', 'description': '', 'run_on': '', 'expiry_date': '', 'frequency': '', 'blocks': ''};
<?php } ?>
        /*  End Objects in the scope  */

        /*  Functions in the scope  */

        /*  End Functions in the scope  */

    })
</script>

<link href="<?php echo $path; ?>Modules/rules/Views/rules.css" rel="stylesheet">

<base href="<?php echo $path; ?>">
<br>
<div ng-app="moduleViewApp" ng-controller="moduleViewAppCtrl" id="edit-rule">
    <div id="apihelphead">
        <div style="float:right;">
            <a ng-disabled="true" href='rules/save-rule?name="{{rule_attibutes.name}}"&description="{{rule_attibutes.description}}"&run_on="{{rule_attibutes.run_on}}"&expiry_date="{{rule_attibutes.expiry_date}}"&frequency={{rule_attibutes.frequency}}&blocks="{{rule_attibutes.blocks}}"&ruleid={{rule_attibutes.ruleid}}'><?php echo _('Save and close') ?></a>
            <a href='rules/save-rule?name="{{rule_attibutes.name}}"&description="{{rule_attibutes.description}}"&run_on="{{rule_attibutes.run_on}}"&expiry_date="{{rule_attibutes.expiry_date}}"&frequency={{rule_attibutes.frequency}}&blocks="{{rule_attibutes.blocks}}"&ruleid={{rule_attibutes.ruleid}}&close=false'><?php echo _('Apply ToDo') ?></a>
            <a href="rules/list"><?php echo _('Cancel') ?></a>
        </div>
    </div>
    <div class="container">
        <div id="localheading"><h2><?php echo _($header); ?></h2></div>
        <div id="rule_saved" ng-if="rule_saved !== null"><p class="bg-success"><?php echo _('Rule saved: ') ?> {{rule_saved}}</p></div>
        <div class="container">
            <table class="table">
                <tr><td><?php echo _('Name') ?>: </td><td><input type="text" ng-model="rule_attibutes.name"/></td></tr>
                <tr><td><?php echo _('Description') ?>: </td><td><input type="text" ng-model="rule_attibutes.description"/></td></tr>
                <tr><td><?php echo _('Run on') ?>: </td><td><input type="datetime" ng-model="rule_attibutes.run_on"/></td></tr>
                <tr><td><?php echo _('Expiry date') ?>: </td><td><input type="datetime" ng-model="rule_attibutes.expiry_date"/></td></tr>
                <tr><td><?php echo _('Frequency') ?>: </td><td><input type="number" ng-model="rule_attibutes.frequency"/></td></tr>
                <!-- <tr id="blocks-programmer"><td><?php //echo _('Blocks')       ?>: </td><td><textarea ng-model="rule_attibutes.blocks"/></td></tr>-->
            </table>
            <div id="blocks-programmer">
                <canvas id="world" tabindex="1" style="position: absolute"/>
            </div>
        </div>
    </div>
</div>






