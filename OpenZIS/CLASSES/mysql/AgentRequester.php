<?php

class AgentRequester extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'agent_requester';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'requester_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'requester_id'=>'requester_id', 
		'agent_id'=>'agent_id', 
		'object_type_id'=>'object_type_id', 
		'requester_timestamp'=>'requester_timestamp', 
		'context_id'=>'context_id', 
		'zone_id'=>'zone_id'
    );
}