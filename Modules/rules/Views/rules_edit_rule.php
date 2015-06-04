<?php
global $path;

// we set the header text according to the mode
if ($args['mode'] == 'add')
    $header = 'New Rule';
else
    $header = 'Edit Rule';

//arrays containing the attributes and feeds (sorted by node) to be used in the visual programmer
global $mysqli, $redis, $session, $route;

include_once "Modules/rules/rules_model.php";
$rules = new Rules($mysqli, $redis);
$array_of_feeds_by_node = $rules->get_user_feeds_by_node($session['userid']); // array like: Array ( [0] => Array ( [id] => 6 [name] => Power [userid] => 1 [tag] => [time] => 1430748375 [value] => 100 [datatype] => 1 [public] => 0 [size] => [engine] => 5 ) [1] => Array ( [id] => 7 [name] => Poadasdawer [userid] => 1 [tag] => [time] => [value] => [datatype] => 1 [public] => 0 [size] => [engine] => 5 ) [2] => Array ( [id] => 8 [name] => Poaeeedasdawer [userid] => 1 [tag] => [time] => [value] => [datatype] => 1 [public] => 0 [size] => [engine] => 5 ));
include_once "Modules/register/register_model.php";
$register = new Register($mysqli);
$array_of_attributes_by_node = $register->getAttributesByNode($session['userid']);
/* echo '<pre>';
  print_r($array_of_attributes_by_node);
  echo '</pre>'; */
?>
<!--<script type="text/javascript" src="<?php //echo $path;        ?>Lib/angularjs/angular.min.js"></script>-->
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
<script type="text/javascript" src="<?php echo $path; ?>Modules/rules/scripts/xml.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/rules/scripts/store.js"></script>


<script>
    var moduleViewApp = angular.module('moduleViewApp', []);

    moduleViewApp.controller('moduleViewAppCtrl', function ($scope, $window) {

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
                'frequency': Number('<?php echo $rule['frequency'] ?>')
                        // we don't include the "blocks" here because they are not used in the angularjs scope, they are used in the Morphic world
            };
            $scope.rule_saved = <?php echo isset($args['rule_saved']) ? json_encode($args['rule_saved']) : "null" ?>;

<?php } else {
    ?>
            $scope.rule_attibutes = {'ruleid': '', 'name': '', 'description': '', 'run_on': '', 'expiry_date': '', 'frequency': '', 'blocks': ''};
            $scope.rule_saved = null;
<?php } ?>
        /*  End Objects in the scope  */

        /*  Functions in the scope  */
        $scope.apply = function () {
            var href = 'rules/save-rule?name=' + $scope.rule_attibutes.name
                    + "&description=" + $scope.rule_attibutes.description
                    + "&run_on=" + $scope.rule_attibutes.run_on
                    + "&expiry_date=" + $scope.rule_attibutes.expiry_date
                    + "&frequency=" + $scope.rule_attibutes.frequency
                    + "&blocks=" + rulesIDE.generateXML()
                    + "&ruleid=" + $scope.rule_attibutes.ruleid
                    + "&close=false";
            $window.location.href = "<?php echo $path; ?>" + href;
        };
        $scope.save = function () {
            var href = 'rules/save-rule?name=' + $scope.rule_attibutes.name
                    + "&description=" + $scope.rule_attibutes.description
                    + "&run_on=" + $scope.rule_attibutes.run_on
                    + "&expiry_date=" + $scope.rule_attibutes.expiry_date
                    + "&frequency=" + $scope.rule_attibutes.frequency
                    + "&blocks=" + rulesIDE.generateXML()
                    + "&ruleid=" + $scope.rule_attibutes.ruleid
                    + "&close=true";
            $window.location.href = "<?php echo $path; ?>" + href;
        };
        /*  End Functions in the scope  */

    })
</script>

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
        var array_of_attributes_by_node = <?php echo json_encode($array_of_attributes_by_node) ?>;
<?php if (isset($rule)) { ?>
            var blocks = '<?php echo str_replace('</script>', "' + '</scr' + 'ipt>' + '", $rule['blocks']) ?>' // This replacement is to avoid echoing "</scrpt>" which would close the javascript scrpt
<?php } else { ?>
            var blocks = null;
<?php } ?>
        rulesIDE = new emonCMS_RulesIDE_Morph(world.width(), world.height(), array_of_feeds_by_node, array_of_attributes_by_node, blocks);
        world.add(rulesIDE);



        setInterval(loop, 1);
    };
    function loop() {
        world.doOneCycle();
    }
</script>





<link href="<?php echo $path; ?>Modules/rules/Views/rules.css" rel="stylesheet">

<base href="<?php echo $path; ?>">
<br>
<div ng-app="moduleViewApp" ng-controller="moduleViewAppCtrl" id="edit-rule">
    <div id="apihelphead">
        <div style="float:right;">
            <span class="like-link" ng-click="save()"><?php echo _('Save and close') ?></span>
            <span class="like-link" ng-click="apply()"><?php echo _('Apply') ?></span>
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
                <!-- <tr id="blocks-programmer"><td><?php //echo _('Blocks')                 ?>: </td><td><textarea ng-model="rule_attibutes.blocks"/></td></tr>-->
            </table>
            <div id="blocks-programmer">
                <canvas id="world" tabindex="1" style="position: absolute"/>
            </div>
        </div>
    </div>
</div>






