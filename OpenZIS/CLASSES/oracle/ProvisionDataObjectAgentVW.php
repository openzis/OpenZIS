<?php

class ProvisionDataObjectAgentVW extends Zend_Db_Table
{

#    protected $_schema = 'OPENZIS';
	protected $_name = 'PROVISION_DATAOBJECT_AGENT_VW';

    protected $_primary = array('provision_id');

    protected $_cols = array(
	'provision_id'=>'provision_id', 
	'zone_id'=>'zone_id',
	'agent_id'=>'agent_id',
	'source_id'=>'source_id', 
	'context_id'=>'context_id',
	'version_id'=>'version_id', 
	'group_id'=>'group_id', 
	'object_id'=>'object_id', 
	'publish_add'=>'publish_add', 
	'publish_delete'=>'publish_delete', 
	'publish_change'=>'publish_change'
    );
}