<?php


class TB_ZoneAuthenticationType extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'zone_authentication_type';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'zone_authentication_type_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'zone_authentication_type_id'=>'zone_authentication_type_id', 
		'zone_authentication_type_desc'=>'zone_authentication_type_desc'
    );
}