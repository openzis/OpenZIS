<?php

/**
 * 
 * @version $Id$
 */
class AgentFilters extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'agent_filters';

    protected $_primary = '';

    protected $_cols = array(
		'zone_id'=>'zone_id', 
		'agent_id'=>'agent_id', 
		'context_id'=>'context_id', 
		'data_object_element_id'=>'data_object_element_id', 
		'data_element_child_id'=>'data_element_child_id'
    );
}