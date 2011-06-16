<?php


class DataObjectElement extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'data_object_element';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'data_object_element_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'data_object_element_id' = 'data_object_element_id', 
		'object_id'=> 'object_id', 
		'element_id'=>'element_id', 
		'can_filter'=>'can_filter'
    );
}