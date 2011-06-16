<?php

class Versions extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'versions';

    protected $_primary = 'version_id';

    protected $_cols = array(
		'version_id'=>'version_id', 
		'version_desc'=>'version_desc', 
		'version_directory'=>'version_directory', 
		'schema_directory'=>'schema_directory', 
		'version_num'=>'version_num', 
		'version_namespace'=>'version_namespace', 
		'active'=>'active'
    );
}