<?php

class AgentRegistered extends Zend_Db_Table
{

    protected $_schema = DBSCHEMA;
    protected $_name = 'agent_registered';

    protected $_primary = 'registration_id';

    protected $_cols = array(
		'registration_id'   => 'registration_id', 
		'agent_id' 			=> 'agent_id', 
		'callback_url'		=> 'callback_url', 
		'agent_mode_id' 	=> 'agent_mode_id', 
		'register_timestamp'=> 'register_timestamp', 
		'unregister_timestamp'=>'unregister_timestamp', 
		'asleep'=>'asleep', 
		'protocol_type'=>'protocol_type', 
		'sif_version' => 'sif_version', 
		'secure'=>'secure', 
		'maxbuffersize'=>'maxbuffersize', 
		'zone_id'=>'zone_id', 
		'context_id'=>'context_id', 
		'frozen'=>'frozen', 
		'frozen_msg_id'=>'frozen_msg_id', 
		'authentication_level_id'=>'authentication_level_id'
    );
}