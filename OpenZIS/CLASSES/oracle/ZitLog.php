<?php

/**
 * 
 * @version $Id$
 */
class ZisLog extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'ZIT_LOG';

    protected $_primary = 'log_id';

    protected $_cols = array(
		'log_id'=>'log_id', 
		'create_timestamp'=>'create_timestamp', 
		'rec_xml'=>'rec_xml', 
		'sent_xml'=>'sent_xml', 
		'zone_id'=>'zone_id',
		'agent_id'=>'agent_id', 
		'sif_message_type_id'=>'sif_message_type_id', 
		'log_message_type_id'=>'log_message_type_id', 
		'archived'=>'archived'
    );
}