<?php

class TB_AdminLevel extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'admin_level';
    protected $_primary = 'level_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		 'level_id' => 'level_id'
		,'level_desc' => 'level_desc'
    );
}