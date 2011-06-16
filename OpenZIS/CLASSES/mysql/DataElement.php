<?php


class DataElement extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'data_element';

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