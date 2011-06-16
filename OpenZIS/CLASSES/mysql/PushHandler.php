<?php

class TB_PushHandler extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'push_handler';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = '';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'ZONE_ID'=>'ZONE_ID', 
		'CONTEXT_ID'=>'CONTEXT_ID', 
		'PUSH_RUNNING'=>'PUSH_RUNNING', 
		'LAST_START'=>'LAST_START', 
		'LAST_STOP'=>'LAST_STOP', 
		'PHP_PID'=>'PHP_PID', 
		'SLEEP_TIME_SECONDS'=>'SLEEP_TIME_SECONDS'
    );
}