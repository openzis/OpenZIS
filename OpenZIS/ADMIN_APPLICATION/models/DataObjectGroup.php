<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class DataObjectGroup
{
	var $dataObjectGroupId;
	var $dataObjectGroupDesc;
	var $versionId;
	var $numObjs;

	public function DataObjectGroup($id){
		$db = Zend_Registry::get('my_db2');
		
		$dog = new DataObjectGroups($db);
		$result = $dog->fetchAll("group_id = $id");
		
		switch(DB_TYPE) {
            case 'mysql':
				$this->dataObjectGroupDesc = $result[0]->group_desc;
				$this->versionId 		   = $result[0]->version_id;
				$this->dataObjectGroupId   = $id;
			break;
			case 'oci8':
				$this->dataObjectGroupDesc = $result[0]->GROUP_DESC;
				$this->versionId 		   = $result[0]->VERSION_ID;
				$this->dataObjectGroupId   = $id;
			break;
		}

		$this->getNumberDataObjects();
	}

	public function getNumberDataObjects(){
		$db = Zend_Registry::get('my_db2');
		$do = new DataObjects($db);
		$result = $do->fetchAll("group_id = $this->dataObjectGroupId");
		
		$this->numObjs = $result->count();
	}

	public static function addDataObjectGroup($name, $version){
		$db = Zend_Registry::get('my_db');
		$data = array(
						DBConvertor::convertCase('group_desc')  => $name,
						DBConvertor::convertCase('version_id')  => $version
					 );

		if($db->insert(DBConvertor::convertCase('data_object_group'), $data)){
			return true;
		}
		else{
			return false;
		}
	}

	public static function updateDataObjectGroup($name, $id, $version){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
		$data = array(
						DBConvertor::convertCase('group_desc')  => $name,
						DBConvertor::convertCase('version_id')  => $version
					 );

		$n = $db->update(DBConvertor::convertCase('data_object_group'), $data, DBConvertor::convertCase('group_id').' = '.$id);
	}

	public static function hasDataObjects($groupId){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
		$do = new DataObjects($db);
		$result = $do->fetchAll("group_id = $groupId");
		
		if($result->count() != 0){
			return true;
		}
		else{
			return true;
		}
	}

	public static function getAllDataObjectsInGroup($groupId){
		$db = Zend_Registry::get('my_db2');
		$dataObjects = array();
		$query = "select ".DBConvertor::convertCase('object_id').", ".DBConvertor::convertCase('object_name')." from ".DBConvertor::convertCase('data_object')." where group_id = ".$groupId." order by object_name";
		$result = $db->fetchAll($query);
		foreach($result as $row){
			switch(DB_TYPE) {
	            case 'mysql':
					$do = new DataObject($row->object_id, $row->object_name);
				break;
				case 'oci8':
					$do = new DataObject($row->OBJECT_ID, $row->OBJECT_NAME);
				break;
			}
			array_push($dataObjects, $do);
		}
		return $dataObjects;
	}

	public static function getAllDataObjectGroups_version($versionId){
		$dataObjectGroups = array();

		$db = Zend_Registry::get('my_db2');

		$query = "select ".DBConvertor::convertCase('group_id')." from ".DBConvertor::convertCase('data_object_group')." where version_id = $versionId order by group_desc";
		$result = $db->fetchAll($query);
		foreach($result as $row){
				switch(DB_TYPE) {
		            case 'mysql':
						$object = new DataObjectGroup($row->group_id);
					break;
					case 'oci8':
						$object = new DataObjectGroup($row->GROUP_ID);
					break;
				}
				array_push($dataObjectGroups, $object);
		}
		return $dataObjectGroups;
	}

	public static function getAllDataObjectGroups(){
		$dataObjectGroups = array();

		$db = Zend_Registry::get('my_db2');
		
		$query = "select ".DBConvertor::convertCase('group_id')." from ".DBConvertor::convertCase('data_object_group')." order by group_desc";
		$result = $db->fetchAll($query);
		foreach($result as $row){
				switch(DB_TYPE) {
		            case 'mysql':
						$object = new DataObjectGroup($row->group_id);
					break;
					case 'oci8':
						$object = new DataObjectGroup($row->GROUP_ID);
					break;
				}
				array_push($dataObjectGroups, $object);
		}
		return $dataObjectGroups;
	}
}

