<?php

/**
 * Zend_Db_Table based simpe ORM for the agent_requester table
 *   timestamp: Sat, 13 Nov 10 15:24:19 -0500
 * 
 * @version $Id$
 */
class AgentRequester extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'AGENT_REQUESTER';

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