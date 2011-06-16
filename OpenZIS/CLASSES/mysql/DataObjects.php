<?php

class DataObjects extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'data_object';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'object_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
	
	'object_name'=>'object_name', 
	'object_id'=>'object_id', 
	'group_id'=>'group_id', 
	'version_id'=>'version_id'
    );
}