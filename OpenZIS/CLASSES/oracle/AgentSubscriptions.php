<?php

class AgentSubscriptions extends Zend_Db_Table
{

#    protected $_schema = 'OPENZIS';
	protected $_name = 'AGENT_SUBSCRIPTIONS';

    protected $_primary = 'subscription_id';

    protected $_cols = array(
		'subscription_id'=>'subscription_id', 
		'agent_id'=>'agent_id', 
		'object_type_id'=>'object_type_id', 
		'subscribe_timestamp'=>'subscribe_timestamp', 
		'context_id'=>'context_id', 
		'zone_id'=>'zone_id'
    );
}