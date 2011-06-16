<?php

class AgentZoneContext extends Zend_Db_Table
{

#    protected $_schema = 'OPENZIS';
	protected $_name = 'AGENT_ZONE_CONTEXT';

    protected $_primary = array('agent_id','zone_id','context_id');

    protected $_cols = array(
	'zone_id'=>'zone_id',
	'agent_id'=>'agent_id', 
	'context_id'=>'context_id'
    );
}