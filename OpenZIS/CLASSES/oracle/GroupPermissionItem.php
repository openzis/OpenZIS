<?php

/**
 * Zend_Db_Table based simpe ORM for the group_permission_item table
 * Autogenerated by db.php:
 *   timestamp: Sat, 13 Nov 10 15:24:19 -0500
 *   database:  openzis
 *   host:      192.168.1.22
 * 
 * @version $Id$
 */
class TB_GroupPermissionItem extends Zend_Db_Table
{
    /**
     * Table name
     * 
     * @var string
     */
#    protected $_schema = 'OPENZIS';
	protected $_name = 'GROUP_PERMISSION_ITEM';

    /**
     * Primary key field
     * 
     * @var string
     */
    protected $_primary = 'group_permission_item_id';

    /**
     * List of columns
     * 
     * @var array
     */
    protected $_cols = array(
		'group_permission_item_id'=>'group_permission_item_id', 
		'object_id'=>'object_id', 
		'can_provide'=>'can_provide', 
		'can_subscribe'=>'can_subscribe', 
		'can_add'=>'can_add', 
		'can_update'=>'can_update', 
		'can_delete'=>'can_delete', 
		'can_request'=>'can_request', 
		'can_respond'=>'can_respond', 
		'group_permission_id'=>'group_permission_id'
    );
}