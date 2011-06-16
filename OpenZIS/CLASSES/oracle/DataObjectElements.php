<?php

/**
 * 
 * @version $Id$
 */
class DataObjectElement extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'DATA_OBJECT_ELEMENT';

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