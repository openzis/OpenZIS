<?php


class TB_SifMessageType extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'sif_message_type';

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