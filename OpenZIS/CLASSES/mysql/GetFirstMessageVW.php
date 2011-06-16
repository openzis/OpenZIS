<?php

class GetFirstMessageVW extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'getfirstmessage_vw';

    protected $_primary = 'zone_id, context_id, agent_id, id';

    protected $_cols = array(
		'zone_id'=>'zone_id', 
		'context_id'=>'context_id', 
		'agent_id'=>'agent_id', 
		'id'=>'id'
    );
}