<?php

class ZisLog extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'zit_log';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'log_id';

    /**
     * List of columns
     * 
     * @var array
     */
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