<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class GroupPermission {

	var $id;
	var $name;
	var $created;
	var $updated;
	var $version;
	var $versionId;

	public function GroupPermission($id){
		
		$db = Zend_Registry::get('my_db2');

		$select = "select
				  g.group_name,
				  g.created_timestamp,
				  v.version_desc,
				  g.updated_timestamp,
				  g.version_id
				  from ".DBConvertor::convertCase('group_permission')." g, ".DBConvertor::convertCase('versions')." v
				  where g.group_permission_id = $id
				  and v.version_id = g.version_id";
				
		$result = $db->fetchAll($select);
		$this->id = $id;
		foreach($result as $row){
			switch(DB_TYPE) {
	            case 'mysql':
					$this->name        = $row->group_name;
					$this->created     = $row->created_timestamp;
					$this->updated     = $row->updated_timestamp;
					$this->version     = $row->version_desc;
					$this->versionId   = $row->version_id;
				break;
				case 'oci8':
					$this->name        = $row->GROUP_NAME;
					$this->created     = $row->CREATED_TIMESTAMP;
					$this->updated     = $row->UPDATED_TIMESTAMP;
					$this->version     = $row->VERSION_DESC;
					$this->versionId   = $row->VERSION_ID;
				break;
			}
		}
	}

	public static function deleteGroup($groupId){
		$db = Zend_Registry::get('my_db');
		$db->delete(DBConvertor::convertCase('group_permission_item'),  DBConvertor::convertCase('group_permission_id').' = '.$groupId);
		$db->delete(DBConvertor::convertCase('group_permission'),  		DBConvertor::convertCase('group_permission_id').' = '.$groupId);
	}

	public static function addGroup($name, $version){
		$db = Zend_Registry::get('my_db');
		$data = array(
						DBConvertor::convertCase('group_name')        => $name,
						DBConvertor::convertCase('created_timestamp') => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
						DBConvertor::convertCase('updated_timestamp') => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
						DBConvertor::convertCase('admin_id')          => $_SESSION['ADMIN_ID'],
						DBConvertor::convertCase('version_id')        => $version
					 );

		if($db->insert(DBConvertor::convertCase('group_permission'), $data)){
			return true;
		}
		else{
			return false;
		}
	}

	public static function useGroupOnAgent($groupId, $agentId, $zoneId, $contextId, $override){
		
		$db = Zend_Registry::get('my_db');

		$items = GroupPermissionItem::getGroupItems($groupId);
		
		foreach($items as $item){
			$exists = Permission::checkIfPermissionExist($zoneId,$agentId,$contextId,$item->objectId);
			if($exists == 1 && $override == 1){
				$db->delete(DBConvertor::convertCase('agent_permissions'),  DBConvertor::convertCase('zone_id').' = '.$zoneId.' and '.DBConvertor::convertCase('agent_id').' = '.$agentId.' and '.DBConvertor::convertCase('context_id').' = '.$contextId);
				Permission::addPermission($zoneId,
										  $agentId,
										  $contextId,
										  $item->objectId,
										  $item->provide,
										  $item->subscribe,
										  $item->add,
										  $item->update,
										  $item->delete_,
										  $item->request,
										  $item->respond);
			}


			if($exists == 0){
				Permission::addPermission($zoneId,
										  $agentId,
										  $contextId,
										  $item->objectId,
										  $item->provide,
										  $item->subscribe,
										  $item->add,
										  $item->update,
										  $item->delete_,
										  $item->request,
										  $item->respond);
			}
		}
	}

	public static function getGroupsByVersion($version){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
		$groups = array();

		$query = "select group_permission_id from ".DBConvertor::convertCase('group_permission')." where version_id = $version and admin_id = ".$_SESSION['ADMIN_ID'];

		$result = $db->fetchAll($query);
		foreach($result as $row){
				switch(DB_TYPE) {
		            case 'mysql':
						$groupPermission = new GroupPermission($row->group_permission_id);
					break;
					case 'oci8':
						$groupPermission = new GroupPermission($row->GROUP_PERMISSION_ID);
					break;
				}
				array_push($groups, $groupPermission);
		}
		return $groups;
	}

	public static function getGroups(){
		$db = Zend_Registry::get('my_db2');
		$groups = array();

		$query = "select group_permission_id from ".DBConvertor::convertCase('group_permission')." where admin_id = ".$_SESSION['ADMIN_ID'];

		$result = $db->fetchAll($query);
		foreach($result as $row){
				switch(DB_TYPE) {
		            case 'mysql':
						$groupPermission = new GroupPermission($row->group_permission_id);
					break;
					case 'oci8':
						$groupPermission = new GroupPermission($row->GROUP_PERMISSION_ID);
					break;
				}
				array_push($groups, $groupPermission);
		}
		return $groups;
	}

}