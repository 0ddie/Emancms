<?php global $path, $session, $user; ?>

<h2><?php echo _('Rules API'); ?></h2>
<h3><?php echo _('Apikey authentication'); ?></h3>
<p><?php echo _('If you want to call any of the following actions when you are not logged in, add an apikey to the URL of your request like: '); ?><a href="<?php echo $path; ?>rules/list.json?apikey=<?php echo $user->get_apikey_write($session['userid']); ?>"><?php echo $path; ?>rules/list.json</a></p>
<p><b><?php echo _('Read only:'); ?></b><br>
    <input type="text" style="width:255px" readonly="readonly" value="<?php echo $user->get_apikey_read($session['userid']); ?>" />
</p>
<p><b><?php echo _('Read & Write:'); ?></b><br>
    <input type="text" style="width:255px" readonly="readonly" value="<?php echo $user->get_apikey_write($session['userid']); ?>" />
</p>

<h3><?php echo _('Available HTML URLs'); ?></h3>
<table class="table">
    <tr><td><?php echo _('The list of rules'); ?></td><td><a href="<?php echo $path; ?>rules/list"><?php echo $path; ?>rules/list</a></td></tr>
    <tr><td><?php echo _('Delete rule - Returns true if success, otherwise false (when rule does not exist or it could not be deleted from the database)'); ?></td><td><a href="<?php echo $path; ?>rules/delete.html?ruleid=1"><?php echo $path; ?>rules/delete.html?ruleid=1</a></td></tr>

</table>

<h3><?php echo _('Available JSON commands'); ?></h3>
<p><?php echo _('To use the json api the request url needs to include <b>.json</b> and the <b>apikey</b> if sent from a node'); ?></p>

<table class="table">
    <tr><td><?php echo _('The list of rules:'); ?></td><td><a href="<?php echo $path; ?>rules/list.json"><?php echo $path; ?>rules/list.json</a></td></tr>
    <tr><td><?php echo _('Delete rule - Returns "success" when rule has been deleted, otherwise "ERROR - message" (when rule does not exist or it could not be deleted from the database)'); ?></td><td><a href="<?php echo $path; ?>rules/delete.json?ruleid=1"><?php echo $path; ?>rules/delete.json?ruleid=1</a></td></tr>
</table>


