<?php

class Responses extends Zend_Db_Table
{

#    protected $_schema = 'OPENZIS';
	protected $_name = 'RESPONSE';

    protected $_primary = 'response_id';

    protected $_cols = array(
		'response_id'		=> 'response_id',
		'request_msg_id'	=> 'request_msg_id', 
		'response_data'		=> 'response_data', 
		'next_packet_num'	=> 'next_packet_num', 
		'status_id'			=> 'status_id', 
		'agent_id_requester'=> 'agent_id_requester', 
		'agent_id_responder'=> 'agent_id_responder', 
		'msg_id'			=> 'msg_id', 
		'agent_mode_id'		=> 'agent_mode_id', 
		'context_id'		=> 'context_id', 
		'zone_id'			=> 'zone_id'
    );
}