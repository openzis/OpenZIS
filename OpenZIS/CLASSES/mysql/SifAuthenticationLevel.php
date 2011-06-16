<?php


class TB_SifAuthenticationLevel extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'sif_authentication_level';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'sif_authentication_level_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'sif_authentication_level_id'=>'sif_authentication_level_id', 
		'sif_authentication_level_desc'=>'sif_authentication_level_desc'
    );
}