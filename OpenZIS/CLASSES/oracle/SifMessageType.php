<?php

/**
 * 
 * @version $Id$
 */
class TB_SifMessageType extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'SIF_MESSAGE_TYPE';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'sif_message_type_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
	'sif_message_type_id'=>'sif_message_type_id', 
	'sif_message_type_desc'=>'sif_message_type_desc'
    );
}