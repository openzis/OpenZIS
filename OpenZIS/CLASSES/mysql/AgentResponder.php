<?php


class AgentResponder extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'agent_responder';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'responder_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'responder_id'=>'responder_id', 
		'agent_id'=>'agent_id', 
		'object_type_id'=>'object_type_id', 
		'responder_timestamp'=>'responder_timestamp', 
		'context_id'=>'context_id', 
		'zone_id'=>'zone_id'
    );
}