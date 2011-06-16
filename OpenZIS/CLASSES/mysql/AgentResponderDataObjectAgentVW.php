<?php

class AgentReponderDataObjectAgentVW extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'agentresponderdataobjectagent_vw';

    protected $_primary = array('responder_id');

    protected $_cols = array(
	'responder_id'=>'responder_id', 
	'agent_id'=>'agent_id', 
	'object_type_id'=>'object_type_id', 
	'responder_timestamp'=>'responder_timestamp', 
	'context_id'=>'context_id',
	'zone_id'=>'zone_id', 
	'source_id'=>'source_id', 
	'version_id'=>'version_id', 
	'object_name'=>'object_name'
 );

}