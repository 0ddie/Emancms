<?php
global $path;
?>

<script type="text/javascript" src="<?php echo $path; ?>Lib/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

<link href="<?php echo $path; ?>Lib/jquery-ui-1.11.4.custom/jquery-ui.min.css" rel="stylesheet">

<style>
    input[type="text"] {
        width: 88%;
    }

    #table td:nth-of-type(1) { width:5%;}
    #table td:nth-of-type(2) { width:10%;}
    #table td:nth-of-type(3) { width:25%;}

    #table td:nth-of-type(7) { width:30px; text-align: center; }
    #table td:nth-of-type(8) { width:30px; text-align: center; }
    #table td:nth-of-type(9) { width:30px; text-align: center; }
</style>

<br>
<div id="apihelphead"><div style="float:right;"><a href="create" style="margin-right:25px"><i class="icon-plus-sign"></i></a><a href="api"><?php echo _('Rules API Help'); ?></a></div></div>

<div class="container">
    <div id="localheading"><h2><?php echo _('Rules'); ?></h2></div>
    <div id="delete-rule-dialog" title="<?php echo _('Do you really want to delete the rule?'); ?>"><?php echo _('If you delete a rule you will lose it forever, be careful!!'); ?></div>
    <div id="list_of_rules">
        <table class="table table-hover">
            <tbody>
                <tr>
                    <th><a><?php echo _('Rule Id'); ?></a></th>
                    <th><a><?php echo _('Name'); ?></a></th>
                    <th><a><?php echo _('Description'); ?></a></th>
                    <th><a><?php echo _('Run on'); ?></a></th>
                    <th><a><?php echo _('Expiry date'); ?></a></th>
                    <th><a><?php echo _('Frequency'); ?></a></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tbody>
            <tbody>
                <?php
                foreach ($args['list_of_rules'] as $rule) {
                    echo '<tr>';
                    echo "<td>" . $rule['ruleid'] . "</td>";
                    echo "<td>" . $rule['name'] . "</td>";
                    echo "<td>" . $rule['description'] . "</td>";
                    echo "<td>" . $rule['run_on'] . "</td>";
                    echo "<td>" . $rule['expiry_date'] . "</td>";
                    echo "<td>" . $rule['frequency'] . "</td>";
                    echo "<td><a href='edit?ruleid=" . $rule['ruleid'] . "'><i class='icon-pencil'></i></a></td>";
                    echo "<td class='delete-dialog-opener'><i class='icon-trash'></i></td>";
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    window.onload = function () {
        $("#delete-rule-dialog").dialog({autoOpen: false});
        $(".delete-dialog-opener").click(function () {
            $("#delete-rule-dialog").dialog("open");
        });
    };
</script>


