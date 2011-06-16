<?php

class DataElementChild extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'data_element_child';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'data_element_child_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'data_element_child_id'=>'data_element_child_id', 
		'parent_element_id'=>'parent_element_id', 
		'child_element_id'=>'child_element_id'
	
    );
}