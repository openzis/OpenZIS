<?php

class AgentPermissions extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'agent_permissions';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'permission_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
	'permission_id' => 'permission_id',
	'agent_id'		=> 'agent_id',
	'context_id'	=> 'context_id',
	'object_id'		=> 'object_id',
	'can_provide'	=> 'can_provide',
	'can_subscribe' => 'can_subscribe',
	'can_add'		=> 'can_add',
	'can_update'	=> 'can_update',
	'can_delete'	=> 'can_delete',
	'can_request'	=> 'can_request',
	'can_respond'	=> 'can_respond',
	'zone_id'		=> 'zone_id',
	'xslt'			=> 'xslt'	
    );
}