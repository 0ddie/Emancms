<?php

$schema['rules'] = array(
    'ruleid' => array('type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
    'userid' => array('type' => 'int(11)', 'Null' => 'NO'),
    'name' => array('type' => 'text'),
    'description' => array('type' => 'text', 'default' => ''),
    'run_on' => array('type' => 'datetime'),
    'expiry_date' => array('type' => 'datetime'),
    'frequency' => array('type' => 'int(11)'),
    'blocks'=> array('type'=>'mediumtext')
);
