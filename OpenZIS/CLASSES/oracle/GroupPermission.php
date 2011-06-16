<?php

/**
 * 
 * @version $Id$
 */
class TB_GroupPermission extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'GROUP_PERMISSION';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'group_permission_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'group_permission_id'=>'group_permission_id', 
		'group_name'=>'group_name', 
		'created_timestamp'=>'created_timestamp', 
		'updated_timestamp'=>'updated_timestamp', 
		'admin_id'=>'admin_id', 
		'version_id'=>'version_id'
    );
}