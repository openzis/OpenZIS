<?php

class TB_GroupPermissionItem extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
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