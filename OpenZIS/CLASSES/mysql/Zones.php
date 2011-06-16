<?php

class Zones extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'zones';

    protected $_primary = 'zone_id';

    protected $_cols = array(
		'zone_id'					=>'zone_id', 
		'zone_desc'					=>'zone_desc',
		'source_id'					=>'source_id', 
		'create_timestamp'			=>'create_timestamp', 
		'update_timestamp'			=>'update_timestamp', 
		'admin_id'					=>'admin_id', 
		'version_id'				=>'version_id', 
		'sleeping'					=>'sleeping', 
		'zone_authentication_type_id'=>'zone_authentication_type_id'
    );
}