<?php

class ZitAdminDB extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'zit_admin';

    protected $_primary = 'admin_id';

	protected $_cols = array(
	'admin_id'          	=> 'admin_id',
	'admin_username'       	=> 'admin_username',
	'admin_password'        => 'admin_password',
	'zit_id'         		=> 'zit_id',
	'admin_level_id'=>'admin_level_id',
	'first_name'=>'first_name', 
	'last_name'=>'last_name', 
	'email'=>'email', 
	'active'=>'active', 
	'last_login'=>'last_login',
	'attempts'=>'attempts',
	'lockout'=>'lockout'
	);
}
