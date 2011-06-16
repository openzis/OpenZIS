<?php

class Agents extends Zend_Db_Table
{
#    protected $_schema = 'OPENZIS';
	protected $_name = 'AGENT';

    protected $_primary = 'agent_id';

    protected $_cols = array(
		'agent_id' 		=> 'agent_id',
		'agent_name' 	=> 'agent_name',
		'source_id'		=> 'source_id',
		'password'		=> 'password',
		'username'		=> 'username',
		'admin_id'		=> 'admin_id',
		'active'		=> 'active',
		'cert_common'	=> 'cert_common_name',
		'cert_common'	=> 'cert_common_dn',
		'ipaddress'		=> 'ipaddress',
		'maxbuffersize'	=> 'maxbuffersize'
    );

}