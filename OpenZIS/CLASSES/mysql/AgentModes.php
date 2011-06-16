<?php

class AgentModes extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'agent_modes';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'agent_mode_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'agent_mode_id' => 'agent_mode_id',
		'mode_desc' 	=> 'mode_desc'
	    );
}