<?php

/**
 * Zend_Db_Table based simpe ORM for the data_object table
 * 
 * @version $Id$
 */
class DataObjects extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'DATA_OBJECT';

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