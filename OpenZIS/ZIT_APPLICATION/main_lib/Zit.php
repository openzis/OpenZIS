<?php /*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Zit{

	public static function isAsleep(){
		
		$asleep = $_SESSION['ASLEEP'];
		
		if($asleep == 1){
			return true;
		}
		else{
			return false;
		}
	}

	public static function getZitUrl(){
		return $_SESSION['ZIT_URL'];
	}

	public static function getAdminUrl(){
		return $_SESSION['ADMIN_URL'];
	}

	public static function getSourceId(){
		return $_SESSION['ZIS_SOURCEID'];
	}

	public static function getZitName(){
		return $_SESSION['ZIS_NAME'];
	}

	public static function getMaxBuffer(){
		return $_SESSION['MAX_BUFFER'];
	}

	public static function getMinBuffer(){
		return $_SESSION['MIN_BUFFER'];
	}

	public static function getVersions(){
		$db = Zend_Registry::get('my_db');
		$versions = array();
		$ver = new Versions($db);
		$where = 'active = 1';
									
		$result = $ver->fetchAll($where);

		foreach($result as $row){
			switch(DB_TYPE) {
				case 'mysql':
					$v = array('VERSION' => $row->version_num);
				break;
				case 'oci8':
					$v = array('VERSION' => $row->VERSION_NUM);
				break;
			}
			array_push($versions, $v);
		}
		return $versions;
	}

	public static function checkVersion($passVer){
		$db = Zend_Registry::get('my_db');
			
			$versions = new Versions($db);
			$quoted = $db->quote($passVer);
			$result = $versions->fetchAll("active = 1 and version_num = $quoted");
		
			foreach($result as $row){
				switch(DB_TYPE) {
					case 'mysql':
						if($passVer == $row->version_num){
							return true;
						}
					break;
					case 'oci8':
						if($passVer == $row->VERSION_NUM){
							return true;
						}
					break;
				}
			}
		return false;
	}
}

