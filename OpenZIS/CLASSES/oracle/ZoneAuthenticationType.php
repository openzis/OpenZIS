<?php

/**
 * 
 * @version $Id$
 */
class TB_ZoneAuthenticationType extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
    protected $_name = 'ZONE_AUTHENTICATION_TYPE';

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