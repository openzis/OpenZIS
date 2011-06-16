<?php

class Requests extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'REQUEST';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'request_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'request_id'=>'request_id', 
		'request_msg_id'=>'request_msg_id', 
		'request_data'=>'request_data', 
		'request_timestamp'=>'request_timestamp', 
		'status_id'=>'status_id', 
		'agent_id_requester'=>'agent_id_requester', 
		'agent_id_responder'=>'agent_id_responder', 
		'max_buffer_size'=>'max_buffer_size', 
		'version'=>'version', 
		'msg_id'=>'msg_id', 
		'agent_mode_id'=>'agent_mode_id', 
		'context_id'=>'context_id', 
		'zone_id'=>'zone_id', 
		'cancel_timestamp'=>'cancel_timestamp'
	
    );
}