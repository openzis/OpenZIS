<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class ZitAdmin
{
	var $id;
	var $username;
	var $fName;
	var $lName;
	var $email;
	var $password;
	var $adminLevelId;
	var $adminLevel;
	var $lastLogin;
	var $active;

	public function ZitAdmin($id)
	{
		$this->id           = $id;
		
		$db = Zend_Registry::get('my_db2');
		$sql = "select
				a.first_name,
				a.active,
				a.admin_password,
				l.level_desc,
				a.admin_level_id,
				".DBConvertor::convertDateFormat('a.last_login', 'm-dd-yyyy-t', 'last_login').",
				a.last_name,
				a.email,
				a.admin_username
				from
				".DBConvertor::convertCase('zit_admin')." a,
				".DBConvertor::convertCase('admin_level')." l
				where a.admin_level_id = l.level_id and a.admin_id = $id";
				
		$result = $db->fetchAll($sql);
		foreach($result as $row){
			switch(DB_TYPE) {
	            case 'mysql':
					$this->username     = $row->admin_username;
					$this->fName        = $row->first_name;
					$this->lName        = $row->last_name;
					$this->email        = $row->email;
					$this->password     = $row->admin_password;
					$this->adminLevel   = $row->level_desc;
					$this->adminLevelId = $row->admin_level_id;
					$this->lastLogin    = $row->last_login;
					$this->active       = $row->active;
				break;
				case 'oci8':
					$this->username     = $row->ADMIN_USERNAME;
					$this->fName        = $row->FIRST_NAME;
					$this->lName        = $row->LAST_NAME;
					$this->email        = $row->EMAIL;
					$this->password     = $row->ADMIN_PASSWORD;
					$this->adminLevel   = $row->LEVEL_DESC;
					$this->adminLevelId = $row->ADMIN_LEVEL_ID;
					$this->lastLogin    = $row->LAST_LOGIN;
					$this->active       = $row->ACTIVE;
				break;
			}
		}
	}

	public static function createAdmin($level,
									   $email,
									   $fName,
									   $lName,
									   $password,
									   $username){

		$db = Zend_Registry::get('my_db');
		$zis = new ZitAdminDB($db);
		
		$data = array(
			  	DBConvertor::convertCase('admin_username') => $username,
				DBConvertor::convertCase('admin_password') => $password,
				DBConvertor::convertCase('admin_level_id') => $level,
				DBConvertor::convertCase('first_name')     => $fName,
				DBConvertor::convertCase('last_name')      => $lName,
				DBConvertor::convertCase('email')          => $email,
				DBConvertor::convertCase('zit_id')         => 1 );

		if($zis->insert($data)) {
			return true;
		} else {
			return false;
		}
	}

	public static function updateAdmin($userId,
				 					   $active,
									   $level,
									   $email,
									   $fName,
									   $lName,
									   $password,
									   $username){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
		$data = array(
						DBConvertor::convertCase('admin_username') => $username,
						DBConvertor::convertCase('admin_password') => $password,
						DBConvertor::convertCase('admin_level_id') => $level,
						DBConvertor::convertCase('first_name')     => $fName,
						DBConvertor::convertCase('last_name')      => $lName,
						DBConvertor::convertCase('email')          => $email,
						DBConvertor::convertCase('active')         => $active
					 );

		$n = $db->update(DBConvertor::convertCase('zit_admin'), $data, DBConvertor::convertCase('admin_id').' = '.$userId);
	}
	
	public static function getAdminLevels(){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
		$levels = array();
		$sql = "select level_id, level_desc from ".DBConvertor::convertCase("admin_level");
		$result = $db->fetchAll($sql);
		foreach($result as $row){
			switch(DB_TYPE) {
				case 'mysql':
				$level = array('DESC'=>$row->level_desc,'ID'=>$row->level_id);
				break;

				case 'oci8':
				$level = array('DESC'=>$row->LEVEL_DESC,'ID'=>$row->LEVEL_ID);
				break;
			}
			array_push($levels, $level);
		}
		return $levels;
	}

	public static function getAllAdmins(){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
		$admins = array();
		$sql = "select admin_id from ".DBConvertor::convertCase("zit_admin")." order by admin_username";
		$result = $db->fetchAll($sql);
		foreach($result as $row){
			switch(DB_TYPE) {
				case 'mysql':
				$admin_id = $row->admin_id;
				break;

				case 'oci8':
				$admin_id = $row->ADMIN_ID;
				break;
			}
			$admin = new ZitAdmin($admin_id);
			array_push($admins, $admin);
		}
		return $admins;
	}
}

