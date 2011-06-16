<?php

/**
 * 
 * @version $Id$
 */
class TB_EventActions extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'EVENT_ACTIONS';

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