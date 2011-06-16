<?php

class TB_EventStatus extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'EVENT_STATUS';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'status_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
	'status_id'=>'status_id', 
	'status_desc'=>'status_desc'
    );
}