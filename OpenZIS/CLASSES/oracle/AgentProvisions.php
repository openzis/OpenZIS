<?php

class AgentProvisions extends Zend_Db_Table
{
#    protected $_schema = 'OPENZIS';
	protected $_name = 'AGENT_PROVISIONS';

    protected $_primary = 'provision_id';

    protected $_cols = array(
		'provision_id'  		=> 'provision_id',
		'agent_id'				=> 'agent_id',
		'object_type_id'		=> 'object_type_id',
		'provision_timestamp' 	=> 'provision_timestamp',
		'context_id'			=> 'context_id',
		'publish_add'			=> 'publish_add',
		'publish_delete'		=> 'publish_delete',
		'publish_change'		=> 'publish_change',
		'zone_id'				=> 'zone_id'
    );
}