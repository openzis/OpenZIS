<?php

class ZisServer extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'zit_server';

    protected $_primary = 'zit_id';

    protected $_cols = array(
		'zit_id'=>'zit_id', 
		'source_id'=>'source_id', 
		'asleep'=>'asleep', 
		'admin_url'=>'admin_url', 
		'zit_name'=>'zit_name', 
		'min_buffer'=>'min_buffer', 
		'max_buffer'=>'max_buffer', 
		'zit_url'=>'zit_url'
    );
}