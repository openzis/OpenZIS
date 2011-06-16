<?php

class TB_EventActions extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'event_actions';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'action_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
	'action_id'=>'action_id', 
	'action_desc'=>'action_desc'
    );
}