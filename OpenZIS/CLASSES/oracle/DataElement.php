<?php

/**
 * 
 * @version $Id$
 */
class DataElement extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'DATA_ELEMENT';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'element_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'element_id'=>'element_id', 
		'element_name'=>'element_name', 
		'xml_tag_name'=>'xml_tag_name'
	
    );
}