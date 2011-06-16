<?php

/**
 * 
 * @version $Id$
 */
class TB_SifAuthenticationLevel extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'SIF_AUTHENTICATION_LEVEL';

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