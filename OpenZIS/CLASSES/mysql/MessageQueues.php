<?php


class MessageQueues extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'messagequeue';

    protected $_primary = 'id';

    protected $_cols = array(
		'id'				=>'id',
		'msg_id'			=>'msg_id', 
		'ack_msg_id'		=>'ack_msg_id',
		'ref_msg_id'		=>'ref_msg_id',  
		'msg_type'			=>'msg_type', 
		'zone_id'			=>'zone_id', 
		'context_id'		=>'context_id', 
		'status_id'			=>'status_id', 
		'data'				=>'data', 
		'agt_id_in'			=>'agt_id_in', 
		'agt_id_out'		=>'agt_id_out', 
		'maxbuffersize'		=>'maxbuffersize', 
		'version'			=>'version',
		'next_packet_num'   =>'next_packet_num',
		'agt_mode_id'		=>'agt_mode_id',
		'object_id'			=>'object_id',
		'action_id'			=>'action_id',
		'insert_timestamp'	=>'insert_timestamp',
		'cancel_timestamp'	=>'cancel_timestamp'
    );
}