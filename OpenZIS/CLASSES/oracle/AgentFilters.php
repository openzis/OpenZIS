<?php

/**
 * 
 * @version $Id$
 */
class AgentFilters extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'AGENT_FILTERS';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = '';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'zone_id'=>'zone_id', 
		'agent_id'=>'agent_id', 
		'context_id'=>'context_id', 
		'data_object_element_id'=>'data_object_element_id', 
		'data_element_child_id'=>'data_element_child_id'
    );
}