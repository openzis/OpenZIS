<?php

class RequestAgentVW extends Zend_Db_Table
{

#    protected $_schema = 'OPENZIS';
	protected $_name = 'REQUESTAGENT_VW';

    protected $_primary = array('request_id');

    protected $_cols = array(
	'request_id'=>'request_id', 
	'version'=>'version', 
	'max_buffer_size'=>'max_buffer_size', 
	'source_id'=>'source_id', 
	'request_msg_id'=>'request_msg_id', 
	'zone_id'=>'zone_id', 
	'context_id'=>'context_id'
    );

}