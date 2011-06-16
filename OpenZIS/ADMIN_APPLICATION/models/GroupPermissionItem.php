<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class GroupPermissionItem{

	var $id;
	var $objectId;
	var $objectName;
	var $provide;
	var $subscribe;
	var $add;
	var $update;
	var $delete_;
	var $request;
	var $respond;
	
	public function GroupPermissionItem($id){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
		
		$select = "select 
				  g.object_id, 
				  g.can_provide, 
				  g.can_subscribe, 
				  g.can_add, 
				  g.can_update, 
				  g.can_delete, 
				  g.can_request, 
				  g.can_respond,
				  o.object_name
				  from ".DBConvertor::convertCase('group_permission_item')." g,
				  ".DBConvertor::convertCase('data_object')." o
				  where o.object_id = g.object_id and g.group_permission_item_id = $id order by o.object_name asc";
				
		$result = $db->fetchAll($select);
		$this->id          = $id;
		switch(DB_TYPE) {
            case 'mysql':
				$this->objectId    = $result[0]->object_id;
				$this->objectName  = $result[0]->object_name;
				$this->provide     = $result[0]->can_provide;
				$this->subscribe   = $result[0]->can_subscribe;
				$this->add         = $result[0]->can_add;
				$this->update      = $result[0]->can_update;
				$this->delete_     = $result[0]->can_delete;
				$this->request     = $result[0]->can_request;
				$this->respond     = $result[0]->can_respond;
			break;
			case 'oci8':
				$this->objectId    = $result[0]->OBJECT_ID;
				$this->objectName  = $result[0]->OBJECT_NAME;
				$this->provide     = $result[0]->CAN_PROVIDE;
				$this->subscribe   = $result[0]->CAN_SUBSCRIBE;
				$this->add         = $result[0]->CAN_ADD;
				$this->update      = $result[0]->CAN_UPDATE;
				$this->delete_     = $result[0]->CAN_DELETE;
				$this->request     = $result[0]->CAN_REQUEST;
				$this->respond     = $result[0]->CAN_RESPOND;
			break;
		}
	}
	
	public static function updateGroupItem(
										 $itemId,
										 $provide,
										 $subscribe,
										 $add,
										 $update,
										 $delete,
										 $request,
										 $respond
									   ){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
		$data = array(
						DBConvertor::convertCase('can_provide')   => Utility::convertCheckBoxValue($provide), 
						DBConvertor::convertCase('can_subscribe') => Utility::convertCheckBoxValue($subscribe), 
						DBConvertor::convertCase('can_add')       => Utility::convertCheckBoxValue($add), 
						DBConvertor::convertCase('can_update')    => Utility::convertCheckBoxValue($update), 
						DBConvertor::convertCase('can_delete')    => Utility::convertCheckBoxValue($delete), 
						DBConvertor::convertCase('can_request')   => Utility::convertCheckBoxValue($request), 
						DBConvertor::convertCase('can_respond')   => Utility::convertCheckBoxValue($respond)
					 );

		$db->update(DBConvertor::convertCase('group_permission_item'), $data, DBConvertor::convertCase('group_permission_item_id').' = '.$itemId);				   
	}
	
	public static function addGroupItem(
										 $groupId,
										 $objectId,
										 $provide,
										 $subscribe,
										 $add,
										 $update,
										 $delete,
										 $request,
										 $respond
									   ){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
		$data = array(
						DBConvertor::convertCase('object_id')     => $objectId, 
						DBConvertor::convertCase('can_provide')   => Utility::convertCheckBoxValue($provide), 
						DBConvertor::convertCase('can_subscribe') => Utility::convertCheckBoxValue($subscribe), 
						DBConvertor::convertCase('can_add')       => Utility::convertCheckBoxValue($add), 
						DBConvertor::convertCase('can_update')    => Utility::convertCheckBoxValue($update), 
						DBConvertor::convertCase('can_delete')    => Utility::convertCheckBoxValue($delete), 
						DBConvertor::convertCase('can_request')   => Utility::convertCheckBoxValue($request), 
						DBConvertor::convertCase('can_respond')   => Utility::convertCheckBoxValue($respond), 
						DBConvertor::convertCase('group_permission_id')      => $groupId
					 );

		if($db->insert(DBConvertor::convertCase('group_permission_item'), $data)){
			return true;
		}
		else{
			return false;
		}							   
	}
	
	public static function dataObjectGroupExist($groupId, $objectId){

		$db = Zend_Registry::get('my_db2');
		
		$select = "select count(*) as NUM_ROWS from ".DBConvertor::convertCase('group_permission_item')." where group_permission_id = $groupId and object_id = $objectId";
		$result = $db->fetchAll($select);
		
		switch(DB_TYPE) {
            case 'mysql':
				$numrows = $result[0]->num_rows;
			break;
			case 'oci8':
				$numrows = $result[0]->NUM_ROWS;
			break;
		}
				
		if($numrows == 0){
			return false;
		}
		else{
			return true;
		}
	}
	
	public static function getGroupItems($groupId){
		$db = Zend_Registry::get('my_db2');
		$items = array();
		
//		$query = "select ".DBConvertor::convertCase('GROUP_PERMISSION_ITEM_ID')." from ".DBConvertor::convertCase('group_permission_item')." where group_permission_id = $groupId order by object_id";

		$query = "select ".DBConvertor::convertCase('GROUP_PERMISSION_ITEM_ID')." from ".DBConvertor::convertCase('group_permission_item')." where group_permission_id = $groupId order by group_permission_item_id";
		
		$result = $db->fetchAll($query);
		foreach($result as $row){
			switch(DB_TYPE) {
	            case 'mysql':
					$groupPermissionItem = new GroupPermissionItem($row->group_permission_item_id);
				break;
				case 'oci8':
					$groupPermissionItem = new GroupPermissionItem($row->GROUP_PERMISSION_ITEM_ID);
				break;
			}
			array_push($items, $groupPermissionItem);
		}
		return $items;
	}
}