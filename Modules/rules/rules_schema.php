<?php

$schema['rules'] = array(
    'ruleid' => array('type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
    'userid' => array('type' => 'int(11)', 'Null' => 'NO'),
    'name' => array('type' => 'text'),
    'description' => array('type' => 'text', 'default' => ''),
    'run_on' => array('type' => 'datetime', 'default' => '0'),
    'expiry_date' => array('type' => 'datetime', 'default' => '0'),
    'frequency' => array('type' => 'int(11)', 'default' => '0'),
    'blocks' => array('type' => 'mediumtext'),
    'enabled' => array('type' => 'tinyint', 'default' => false)
);
$schema['rules_pending_acks'] = array(
    'id' => array('type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
    'request_time' => array('type' => 'datetime'),
    'type' => array('type' => 'text', 'Null' => 'NO'),
    'ruleid' => array('type' => 'int(11)'),
    'next_stage' => array('type' => 'int(11)', 'default' => ''),
    'args' => array('type' => 'text'),
    'timeout' => array('type' => 'int(11)', 'default' => '0')
);
