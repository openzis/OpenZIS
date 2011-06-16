<?php

class Versions extends Zend_Db_Table
{
#    protected $_schema = 'OPENZIS';
	protected $_name = 'VERSIONS';

    protected $_primary = 'VERSION_ID';

    protected $_cols = array(
		'VERSION_ID'=>'VERSION_ID', 
		'VERSION_DESC'=>'VERSION_DESC', 
		'VERSION_DIRECTORY'=>'VERSION_DIRECTORY', 
		'SCHEMA_DIRECTORY'=>'SCHEMA_DIRECTORY', 
		'VERSION_NUM'=>'VERSION_NUM', 
		'VERSION_NAMESPACE'=>'VERSION_NAMESPACE', 
		'ACTIVE'=>'ACTIVE'
    );
}