<?php

/**
 * Zend_Db_Table based simpe ORM for the log_message_type table
 * 
 * @version $Id$
 */
class TB_LogMessageType extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'LOG_MESSAGE_TYPE';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'log_message_type_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
	'log_message_type_id'=>'log_message_type_id', 
	'log_message_type_desc'=>'log_message_type_desc'
    );
}