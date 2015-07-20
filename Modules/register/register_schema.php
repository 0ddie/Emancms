<?php

$schema['Node_reg'] = array(
    'NodeID' => array('type' => 'int(11)', 'Null'=>'NO',),
    'FromAddress' => array('type' => 'varchar(255)', 'Null'=>'YES',),
    'MACAddress' => array('type' => 'text'), 
    'userid' => array('type' => 'int(11)',)
);

$schema['attributes'] = array(
    'attributeUid' => array('type' => 'int(11)', 'Null'=>'NO','Key'=>'PRI','Extra'=>'auto_increment',),
    'nodeid' => array('type' => 'int(11)', 'Null'=>'NO',),
    'groupid' => array('type' => 'varchar(255)', 'Null'=>'NO',),
    'attributeId' => array('type' => 'varchar(255)', 'Null'=>'NO',),
    'attributeNumber' => array('type' => 'varchar(255)', 'Null'=>'NO',),
    'attributeDefaultValue' => array('type' => 'varchar(255)', 'Null'=>'NO',),
    'inputId' => array('type' => 'int(11)', 'Null'=>'NO',),
    'feedId' => array('type' => 'int(11)', 'Null'=>'NO',),
    'userid' => array('type' => 'int(11)', 'Null'=>'NO',),
);

$schema['attribute_information'] = array(
    'identifier' => array('type' => 'text','Null'=>'NO',),
    'GroupID' => array('type' => 'text','Null'=>'NO',),
    'Name' => array('type' => 'text','Null'=>'NO',),
    'type' => array('type' => 'text','Null'=>'NO',),
    'Min' => array('type' => 'int(11)','Null'=>'NO',),
    'Max' => array('type' => 'int(11)','Null'=>'NO',),
    'Default_Value' => array('type' => 'text','Null'=>'NO',),
    'Mandatory/Optional' => array('type' => 'tinyint(1)','Null'=>'NO',),
    'UUID' => array('type' => 'int(11)', 'Null'=>'NO','Extra'=>'auto_increment','Key'=>'PRI',),
);

$schema['groupids'] = array(
    'ID' => array('type' => 'text','Null'=>'NO',),
    'Name' => array('type' => 'text','Null'=>'NO',),
    'Description' => array('type' => 'text','Null'=>'NO',),
    'UUID' => array ('type' => 'int','Null'=>'NO', 'auto_increment','Key'=>'PRI',)

);
