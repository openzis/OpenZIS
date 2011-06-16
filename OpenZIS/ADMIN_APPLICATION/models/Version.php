<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Version
{
	var $id;
	var $desc;
	var $numGroups;

	public function Version($id){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
		$v = new Versions($db);
		$result = $v->fetchAll("version_id = ".$id);

		$this->id      = $id;
		switch(DB_TYPE) {
            case 'mysql':
				$this->desc    = $result[0]->version_desc;
			break;
			case 'oci8':
				$this->desc    = $result[0]->VERSION_DESC;
			break;
		}
		$this->getNumberDataObjectGroups();
	}

	public static function getAllVersions(){
		$versions = array();

		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
		
		$v = new Versions($db);
		$result = $v->fetchAll("active = 1");

		foreach($result as $row){
				switch(DB_TYPE) {
		            case 'mysql':
						$version = new Version($row->version_id);
					break;
					case 'oci8':
						$version = new Version($row->VERSION_ID);
					break;
				}
				array_push($versions, $version);
		}
		return $versions;
	}

	public function getNumberDataObjectGroups(){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
		$dog = new DataObjectGroups($db);
		$result = $dog->fetchAll("version_id = $this->id");

//		$query = "select count(*) as num_rows from data_object_group where version_id = $this->id";
//		$result = $db->fetchAll($query);
//		$this->numGroups = $result[0]->num_rows;
		$this->numGroups = $result->count();
	}

	public function hasDataObjectGroups(){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
		$dog = new DataObjectGroups($db);
		$result = $dog->fetchAll("version_id = $this->id");

//		$query = "select count(*) as num_rows from data_object_group where version_id = $this->id";
//		$result = $db->fetchAll($query);
		if($result->count()!= 0){
			return true;
		}
		else{
			return false;
		}
	}
}