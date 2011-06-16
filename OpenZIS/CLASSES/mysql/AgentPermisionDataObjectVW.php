<?php

class AgentPermisionDataObjectVW extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'agentpermisiondataobject_vw';

    protected $_primary = array('provision_id');

    protected $_cols = array(
	'provision_id'=>'provision_id', 
	'zone_id'=>'zone_id',
	'agent_id'=>'agent_id', 
	'context_id'=>'context_id',
	'version_id'=>'version_id', 
	'group_id'=>'group_id', 
	'object_id'=>'object_id', 
	'object_name'=>'object_name',
	'can_provide'=>'can_provide',
	'can_subscribe'=>'can_subscribe', 
	'can_add'=>'can_add', 
	'can_update'=>'can_update', 
	'can_delete'=>'can_delete', 
	'can_request'=>'can_request', 
	'can_respond'=>'can_respond'
    );
}