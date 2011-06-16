<?php

class ErrorLog extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'error_log';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'error_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'error_id'=>'error_id', 
		'short_error_desc'=>'short_error_desc', 
		'long_error_desc'=>'long_error_desc', 
		'error_location'=>'error_location', 
		'error_timestamp'=>'error_timestamp', 
		'zone_id'=>'zone_id', 
		'agent_id'=>'agent_id', 
		'context_id'=>'context_id', 
		'archived' => 'archived'
    );
}