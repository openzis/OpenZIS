<?php


class Events extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'event';

    protected $_primary = 'event_id';

    protected $_cols = array(
		'event_id'=>'event_id',
		'event_timestamp'=>'event_timestamp', 
		'agent_id_sender'=>'agent_id_sender', 
		'agent_id_rec'=>'agent_id_rec', 
		'event_data'=>'event_data', 
		'object_id'=>'object_id', 
		'status_id'=>'status_id', 
		'action_id'=>'action_id', 
		'msg_id'=>'msg_id', 
		'agent_mode_id'=> 'agent_mode_id', 
		'context_id'=>'context_id', 
		'zone_id'=>'zone_id'
    );
}